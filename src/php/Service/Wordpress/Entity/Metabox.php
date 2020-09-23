<?php


namespace WPPluginCore\Service\Wordpress\Entity;

use WP_Post;
use WPPluginCore\Logger;
use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\View\Implementation\MetaboxWrapper;
use WPPluginCore\View\Implementation\Metabox as MetaboxView;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity as WPEntityDAO;


class Metabox extends Service
{

    private WPEntityDAO $wpEntityDAO;
    private MetaboxWrapper $metaboxWrapper;
    private MetaboxView $metaboxView;


    public function __construct(LoggerInterface $logger, WPEntityDAO $wpEntityDAO, MetaboxWrapper $metaboxWrapper, MetaboxView $metaboxView)
    {
        parent::__construct($logger);
        $this->wpEntityDAO = $wpEntityDAO;
        $this->metaboxWrapper = $metaboxWrapper;
        $this->metaboxView = $metaboxView;
    }

    /**
     * @param WPEntity $entity
     */
    public function editor(WPEntity $entity): void
    {
        $this->metaboxWrapper->slug = $entity::getSlug();
        $this->metaboxWrapper->html = $this->createHTML($entity);
        $this->metaboxView->show();
    }

    protected function createHTML(WPEntity $entity) : string
    {
        $output = '';
        foreach ($entity->getAttributes() as $key => $value) {
            $output .= $value->getAdminHTML();
        }
        return $output;
    }

    /**
     * Registers the default metabox for an WPEntity
     *
     * @param string $slug
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function registerDefaultMetaBox(string $slug)
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
}
