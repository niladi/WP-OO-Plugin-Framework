<?php


namespace WPPluginCore\Service\Wordpress\Entity;

use WP_Post;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\ApplicationEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\PayoutEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\Domain\Entity\Implementation\LicencePayout;
use WPPluginCore\Persistence\Domain\Entity\Implementation\LicenceApplication;
use WPPluginCore\Persistence\Domain\Entity\Implementation\AffiliateApplication;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Assets\Implementation\PDF;
use WPPluginCore\View\Implementation\AjaxButton;
use WPPluginCore\View\Implementation\Metabox as MetaboxView;
use WPPluginCore\View\Implementation\PDFButton;

class Metabox extends Service
{
    private array $customContent = array();


    /**
     * Void adds the custom content functions to the metaboxes
     *
     * @param string $class the class of the WPEntity
     * @param callable $callback the callback function of the which echos which 
     * @return self for better methode chaining
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function addCustomContent(string $class, callable $callback) : self
    {
        if (!is_subclass_of($class, WPEntity::class)) {
            throw new IllegalStateException('The class shoudl be of type: ' . WPEntity::class);
        }
        $this->customContent[$class] = $callback;
        return $this;
    }

    /**
     * @param WPEntity $entity
     */
    public function editor(WPEntity $entity): void
    {
        foreach ($this->customContent as $key => $value) {
            if ($entity instanceof $key) {
                call_user_func($value, $entity);
            }
        }
        $output = '';
        foreach ($entity->getAttributes() as $key => $value) {
            $output .= $value->getAdminHTML();
        }
        MetaboxView::show(array(MetaboxView::SLUG => $entity::getSlug(), MetaboxView::HTML => $output));
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
        $factory = EntityFactory::getInstance();
        $dao = $factory->getWPEntityDAOInstanceBySlug($post->post_type);
        $entity = $dao->readFromWPPostID($post->ID);
        if (!$entity) {
            $entity =  $factory->newEntity($dao, (array(WPEntity::KEY_WP_POST_ID => $post->ID)));
        }
        $this->editor($entity);
    }
}
