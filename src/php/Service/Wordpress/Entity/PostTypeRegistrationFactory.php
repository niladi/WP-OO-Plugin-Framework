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
use WPPluginCore\Service\Wordpress\Abstraction\Menu;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

class PostTypeRegistrationFactory
{

    private WPEntityContainer $wpEntityContainer;
    private Metabox $metabox;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }

    public function create(string $entityClass, Metabox $metabox) : PostTypeRegistration
    {
        return new PostTypeRegistration($this->logger, $metabox, $entityClass);
    }

}
