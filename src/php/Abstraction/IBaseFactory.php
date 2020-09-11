<?php


namespace WPPluginCore\Abstraction;

defined('ABSPATH') || exit;

/**
 * The Interface for each factory
 * 
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
interface IBaseFactory
{
    /**
     * return the instance if instanciated
     * 
     * @return static
     */
    public static function getInstance();
}