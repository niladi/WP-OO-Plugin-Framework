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
abstract class Registerable implements IRegisterable
{

    protected bool $registered = false;

    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        if ($this->registered) {
            throw new IllegalStateException('Register me should not called twice: ' . static::class);
        }
        $this->registered = true;
    }

    /**
     * @inheritDoc
     */
    public function isRegistered() : bool
    {
        return $this->registered;
    }
}
