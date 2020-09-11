<?php


namespace WPPluginCore\Service\Abstraction;
defined('ABSPATH') || exit;

use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Abstraction\RegisterableFactory;

abstract class Service extends RegisterableFactory
{
    /**
     * @var static[] the array of Instances
     */
    private static array $_instances = array();

    /**
     * @inheritDoc
     * 
     * @return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }

    protected function __construct()
    {
    }



}
