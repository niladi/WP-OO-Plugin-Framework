<?php
namespace WPPluginCore\Abstraction;

use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalStateException;

defined('ABSPATH') || exit;
interface IRegisterable 
{

    /**
     * Registers the RegisterableFactory class, and sets the registered value on true
     *
     * @return void
     * @throws IllegalStateException if the class is already registerd
     * 
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    static public function registerMe(Plugin $plugin): void  ;

    /**
     * Gets the value of the registered state
     *
     * @return boolean the registered state
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function isRegistered() : bool;
}