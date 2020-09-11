<?php


namespace WPPluginCore\Persistence\DAO\Abstraction;

defined('ABSPATH') || exit;

use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Service\Abstraction\Service;

abstract class DAO implements IBaseFactory
{
    /**
     * @var static[] the array of Instances
     */
    private static array $_instances = array();

    /**
     * @inheritDoc
     */
    public static function getInstance() 
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }
}
