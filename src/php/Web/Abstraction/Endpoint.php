<?php


namespace WPPluginCore\Web\Abstraction;

defined('ABSPATH') || exit;

use WPPluginCore\Abstraction\Registale;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Abstraction\RegisterableFactory;

/**
 * Abstract Model for the REST/Ajax endpoints
 * 
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
abstract class Endpoint extends RegisterableFactory
{

    /**
     * @inheritDoc
     */
    private static $_instances = array();

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

    protected function __construct()
    {
    }

}
