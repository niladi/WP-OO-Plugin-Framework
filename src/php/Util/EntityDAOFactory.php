<?php


namespace WPPluginCore\Util;

use function DI\factory;

use Psr\Log\LoggerInterface;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Wordpress\Entity\Save;
use DI\Definition\Helper\FactoryDefinitionHelper;
use WPPluginCore\Persistence\DAO\Adapter\DBConnector;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\DAO\Entity\Container\EntityContainer;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

defined('ABSPATH') || exit;


class EntityDAOFactory
{
    public static function wPEntityFactory(EntityFactory $entityFactory, EntityContainer $entityContainer, DBConnector $dBConnector, LoggerInterface $logger, WPEntityContainer $wPEntityContainer, Save $save) : FactoryDefinitionHelper
    {
        return factory(fn(EntityFactory $entityFactory, EntityContainer $entityContainer, DBConnector $dBConnector, LoggerInterface $logger, WPEntityContainer $wPEntityContainer, Save $save) 
        => new WPEntity($entityFactory, $entityContainer, $dBConnector, $logger, $wPEntityContainer, $save));
    }

    public static function entityFactory(EntityFactory $entityFactory, EntityContainer $entityContainer, DBConnector $dBConnector, LoggerInterface $logger): FactoryDefinitionHelper
    {
        return factory(fn(EntityFactory $entityFactory, EntityContainer $entityContainer, DBConnector $dBConnector, LoggerInterface $logger) 
        => new Entity($entityFactory, $entityContainer, $dBConnector, $logger));
    }
}