<?php


namespace WPPluginCore\Service\Wordpress\Entity;
defined('ABSPATH') || exit;

use WP_Post;
use WPPluginCore\Domain;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\Persistence\DAO;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Entity\Metabox;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

class PostTypeRegistration extends Service
{

    private string $entityClass;
    private Metabox $metabox;
    private Menu $menu;

    public function __construct(LoggerInterface $logger, Metabox $metabox,Menu $menu, string $entityClass)
    {
        parent::__construct($logger);
        if (!is_subclass_of($entityClass, WPEntity::class)) {
            throw new IllegalArgumentException("The entity class $entityClass is not of tyoe ${WPEntity::class}");
        }
        $this->menu = $menu;
        $this->entityClass = $entityClass;
        $this->metabox = $metabox;

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
        $this->metabox->registerDefaultMetaBox($slug);
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        parent::registerMe();
        add_action('init', array($this, 'register'));

    }
}
