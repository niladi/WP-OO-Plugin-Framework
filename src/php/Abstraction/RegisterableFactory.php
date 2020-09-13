<?php


namespace WPPluginCore\Abstraction;

use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalStateException;

defined('ABSPATH') || exit;

/**
 * An Abstract registable factory
 * 
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
abstract class RegisterableFactory implements IRegisterable, IBaseFactory
{

    protected bool $registered = false;

    /**
     * @inheritDoc
     */
    public static function registerMe() : void
    {
        if (static::getInstance()->registered) {
            throw new IllegalStateException('Register me should not called twice: ' . static::class);
        }
        static::getInstance()->registered = true;
    }

    /**
     * @inheritDoc
     */
    public static function isRegistered() : bool
    {
        return static::getInstance()->registered;
    }

    protected function __construct() 
    {
        $this->registered = false;
    }
}
