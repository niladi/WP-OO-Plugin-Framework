<?php


namespace WPPluginCore\Service\Wordpress\Entity;
defined('ABSPATH') || exit;

use WP_Post;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\Persistence\DAO;
use WPPluginCore\Domain;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Entity\Metabox;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;
use WPPluginCore\Service\Wordpress\Menu;

class PostTypeRegistrationFactory
{

    private LoggerInterface $logger;
    private Menu $menu;

    public function __construct(LoggerInterface $logger, Menu $menu) 
    {
        $this->menu = $menu;
        $this->logger = $logger;
    }

    public function create(string $entityClass, Metabox $metabox) : PostTypeRegistration
    {
        return new PostTypeRegistration($this->logger, $metabox, $entityClass);
    }

}
