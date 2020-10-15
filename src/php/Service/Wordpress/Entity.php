<?php


namespace WPPluginCore\Service\Wordpress;

use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\View\Implementation\Metabox;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\View\Implementation\MetaboxWrapper;
use WPPluginCore\Domain\Entity\Abstraction\EntityValidator;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity as WPEntityDAO;

defined('ABSPATH') || exit;

/**
 * The Service for the databse setup and destrcut
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class Entity extends Service {
    
    private WPEntityDAO $wpEntityDAO;
    private ?EntityValidator $entityValidator;
    private string $entityClass;
    private Menu $menu;
    private MetaboxWrapper $metaboxWrapper;
    private Metabox $metaboxView;


    public function __construct(LoggerInterface $logger, WPEntityDAO $wpEntityDAO,Menu $menu, string $entityClass, MetaboxWrapper $metaboxWrapper, Metabox $metaboxView, ?EntityValidator $entityValidator = null)
    {
        parent::__construct($logger);
        $this->wpEntityDAO = $wpEntityDAO;
        $this->entityValidator = $entityValidator;
        if (!is_subclass_of($entityClass, WPEntity::class)) {
            throw new IllegalArgumentException("The entity class $entityClass is not of tyoe ${WPEntity::class}");
        }
        $this->menu = $menu;
        $this->entityClass = $entityClass;
        $this->metaboxWrapper = $metaboxWrapper;
        $this->metaboxView = $metaboxView;
    }

    /*
     * **************************************************************
     * Metabox SECTION
     * **************************************************************
     *
     */

    /**
     * @param WPEntity $entity
     */
    public function editor(WPEntity $entity): void
    {
        $this->metaboxWrapper->wpEntity = $entity;
        $this->metaboxView->show();
    }

    /**
     * Registers the default metabox for an WPEntity
     *
     * @param string $slug
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function registerDefaultMetaBox(string $slug) : void
    {
        add_action('add_meta_boxes_' . $slug, array($this, 'addMetaBox'));
        remove_post_type_support($slug, 'editor');
    }

    /**
     * add the metabox / metaboxes to the post type
     *
     * @param string $slug the slug of the metabox
     */
    public function addMetaBox(WP_Post $post): void
    {
        $slug = $post->post_type;
        add_meta_box(
            $slug . '_editor',
            __('Informationen', 'wp-plugin-core'),
            array( $this, 'editorByPost' ),
            $slug,
            'advanced',
            'high'
        );
    }



    /**
     * Creates the editor by the WP_Post. Should be executed by wordpress add_meta_boxes callback.
     *
     * @param WP_Post $post the current WP_Post which is displayed
     *
     */
    public function editorByPost(WP_Post $post) : void
    {
        $entity = $this->wpEntityDAO->readFromWPPostID($post->ID);
        if (!$entity) {
            if ( $this->wpEntityDAO->createByArray(array(WPEntity::KEY_WP_POST_ID => $post->ID))) {
                $entity = $this->wpEntityDAO->readFromWPPostID($post->ID);
            } else {
                $this->logger->error('Can`t create database entry');
                exit;
            }
        }
        $this->editor($entity);
    }

    /*
     * **************************************************************
     * Register Post Type SECTION
     * **************************************************************
     *
     */

         /**
     * Register the Post type
     *
     * @param $labels array the Labels for menu etc
     * @param string $class the slug of the postType
     */
    final public function register() : void
    {
        $labels = $this->entityClass::getLabels();
        $slug = $this->entityClass::getSlug();

        $args = array(
            'label'               => $labels['name'],
            'labels'              => $labels,
            'supports'            => array(),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'capability_type'     => 'post',
            'show_in_menu'        => $this->menu->getSlug()
        );

        register_post_type($slug, $args);
        $this->registerDefaultMetaBox($slug);
    }

    /*
     * **************************************************************
     * Save Post Type SECTION
     * **************************************************************
     *
     */

    
    /**
     * Helper function to update the wordpress title / name of the post
     *
     * @param WPEntity $entity the entity which should get updated
     */
    final public function updateTitle(WPEntity $entity) : void
    {
        $this->removeSaveAction();
        $title = $entity->getWPPostTitle();
        $values = array(
            'post_title' => $title,
            'post_name' => $title,
            'ID' => $entity->getWPPostID()
        );
        wp_update_post($values);
        $this->addSaveAction();
    }


    /**
     * check if the post save is valid
     *
     * @param $post_id int the post id you want to set
     *
     * @return bool true if the post id is saveable
     */
    final protected function checkPostValidMetaSave(int $post_id, string $slug): bool
    {
        if (wp_is_post_revision($post_id)) {
            return false;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        if (! isset($_POST[ $slug  . '_meta_box_nonce' ])) {
            return false;
        }

        if (! wp_verify_nonce($_POST[ $slug . '_meta_box_nonce' ], $slug . '_save_meta_box_data')) {
            return false;
        }

        if ($slug  == $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id) || ! current_user_can('edit_post', $post_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * save the with id (from wp-admin)
     *
     * @param $post_id int id of post
     */
    public function savePost(int $post_id): void
    {
        $slug = get_post_type( $post_id );
        if ($this->checkPostValidMetaSave($post_id, $slug)) {
            $this->logger->debug('Trying to save');

            $entity = $this->entityClass::init();
            foreach ($entity->getAttributes() as $attribute) {
                try {
                    $attribute->loadFromPost();
                } catch (AttributeException $e) {
                    wp_die($e->getMessage());
                }
            }
            if ($this->entityValidator && $this->entityValidator->isValid($entity)) {
                if ($entity->getID() == -1) {
                    if ($this->wpEntityDAO->create($entity) === false) { 
                        $this->logger->error('Can\t save the post: ', (array) $entity);
                    }
                } else {
                    $this->wpEntityDAO->update($entity);
                }
            } else {
                // todo Notification handler
                $this->logger->info("the post is not valid");
            }
        }
    }

    final private function addSaveAction() : void
    {
        add_action('save_post', array( $this, 'savePost' ));
    }

    final private function removeSaveAction() : void
    {
        remove_action('save_post', array( $this, 'savePost' ));
    }


    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        parent::registerMe();
        $this->addSaveAction();
        add_action('init', array($this, 'register'));
    }

}