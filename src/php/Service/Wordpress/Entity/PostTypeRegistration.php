<?php


namespace WPPluginCore\Service\Wordpress\Entity;
defined('ABSPATH') || exit;

use WP_Post;
use WPPluginCore\Plugin;
use WPPluginCore\Persistence\DAO;
use WPPluginCore\Persistence\Domain;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Entity\Metabox;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Service\Wordpress\Abstraction\Menu;
use WPPluginCore\Service\Wordpress\Entity\Metaboxes;

class PostTypeRegistration extends Service
{
    /**
     * Returns the labels for initializing the WPEntity
     *
     * @param string $slug the key in entities
     *
     * @return array the array of the labels
     * @throws IllegalKeyException if the key does not exists
     */
    private function getLabels(string $slug) : array
    {
        if (!key_exists($slug, $this->entities)) {
            throw new IllegalKeyException();
        }
        return call_user_func(array($this->entities[$slug]["domain"], 'getLabels'));
    }



    /*
     * **************************************************************
     * Wordpress Registration SECTION
     * *************************************************************
     *
     */

    /**
     * Register the Post type
     *
     * @param $labels array the Labels for menu etc
     * @param string $class the slug of the postType
     */
    final private static function register(string $class) : void
    {
        $labels = $class::getLabels();
        $slug = $class::getSlug();

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
            'show_in_menu'        => $class::getMenuSlug()
        );

        register_post_type($slug, $args);
        Metabox::getInstance()->registerDefaultMetaBox($slug);
    }

    /**
     * @inheritDoc
     */
    static public function registerMe(Plugin $plugin): void 
    {
        parent::registerMe($plugin);
        add_action('init', array(self::getInstance(), 'registerPostTypes'));

    }

    /**
     * Registeres the post types
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function registerPostTypes() : void
    {
        foreach (EntityFactory::getInstance()->getWPEntites() as $class) {
            self::register($class);
        }
    }
}
