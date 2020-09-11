<?php


namespace WPPluginCore\Service\Wordpress\Entity;

use WP_Post;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\ApplicationEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\PayoutEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\Domain\Entity\Implementation\LicencePayout;
use WPPluginCore\Persistence\Domain\Entity\Implementation\LicenceApplication;
use WPPluginCore\Persistence\Domain\Entity\Implementation\AffiliateApplication;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Assets\Implementation\PDF;
use WPPluginCore\View\Implementation\AjaxButton;
use WPPluginCore\View\Implementation\Metabox;
use WPPluginCore\View\Implementation\PDFButton;

class Metaboxes extends Service
{
    private array $customContent = array();


    public function addCustomContent(string $slug, callable $callback)
    {
        $this->customContent[$slug] = $callback;
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
        Metabox::show(array(Metabox::SLUG => $entity::getSlug(), Metabox::HTML => $output));
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

    /**
     * Custom editor for an payout
     *
     * @param WPEntity $payout
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function editorPayout(PayoutEntity $payout): void
    {
        PDFButton::show(array(PDFButton::PAYOUT_ID => $payout->ID));
    }

    /**
     * custom additonal fields got applications
     *
     * @param WPEntity $application
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function editorApplication(ApplicationEntity $application): void
    {
        $state = $application->getAttributeValue($application::KEY_STATE);
        if ($state == $application::STATE_OPEN) {
            AjaxButton::show(array(AjaxButton::KEY_FUNCTION => 'accept_application', AjaxButton::KEY_LABEL => __('Akzeptiere Bewerbung', 'wp-plugin-core')));
            AjaxButton::show(array(AjaxButton::KEY_FUNCTION => 'decline_application', AjaxButton::KEY_LABEL =>__('Lehne Bewerbung ab', 'wp-plugin-core')));
        } elseif ($application->getAttributeValue($application::KEY_STATE) == $application::STATE_ACCEPTED) {
            AjaxButton::show(array(AjaxButton::KEY_FUNCTION => 'disaccept_application', AjaxButton::KEY_LABEL =>__('Bewerbung wieder öffnen', 'wp-plugin-core')));
        }
    }
}
