<?php


namespace WPPluginCore\Util;

use function DI\factory;

use Psr\Log\LoggerInterface;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Wordpress\Entity\Save;
use DI\Definition\Helper\FactoryDefinitionHelper;
use WPPluginCore\Persistence\DB\DBConnector;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

defined('ABSPATH') || exit;


class EntityDAOFactory
{
    public static function wPEntityFactory(EntityFactory $entityFactory,  DBConnector $dBConnector, LoggerInterface $logger, Save $save) : FactoryDefinitionHelper
    {
        return factory(fn(EntityFactory $entityFactory, DBConnector $dBConnector, LoggerInterface $logger, Save $save) 
        => new WPEntity($entityFactory,  $dBConnector, $logger, $save));
    }

    public static function entityFactory(EntityFactory $entityFactory, DBConnector $dBConnector, LoggerInterface $logger): FactoryDefinitionHelper
    {
        return factory(fn(EntityFactory $entityFactory, DBConnector $dBConnector, LoggerInterface $logger) 
        => new Entity($entityFactory, $dBConnector, $logger));
    }
}