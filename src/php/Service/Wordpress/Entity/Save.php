<?php


namespace WPPluginCore\Service\Wordpress\Entity;

use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\Domain;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity as WPEntityDAO;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

class Save extends Service
{

    private WPEntityDAO $wpEntityDAO;

    public function __construct(LoggerInterface $logger, WPEntityDAO $wpEntityDAO)
    {
        parent::__construct($logger);
        $this->wpEntityDAO = $wpEntityDAO;
    }

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

            $entity = $this->wpEntityDAO->getEntityFactory()->entity();
            foreach ($entity->getAttributes() as $attribute) {
                try {
                    $attribute->loadFromPost();
                } catch (AttributeException $e) {
                    wp_die($e->getMessage());
                }
            }
            if ($entity->getID() == -1) {
                if ($this->wpEntityDAO->create($entity) === false) {
                    $this->logger->error('Can\t save the post: ', (array) $entity);
                }
            } else {
                $this->wpEntityDAO->update($entity);
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
    }
}
