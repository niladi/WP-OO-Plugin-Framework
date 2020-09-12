<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Implementation;

use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use WPPluginCore\Service\Wordpress\Ressource\Abstraction\Ressource;

defined('ABSPATH') || exit;

class Metabox extends Ressource
{

    public const KEY_CUSTOM_METABOX = 'custom_metabox_css';

    /**
     * @inheritDoc
     */
    protected static function getType() : int
    {
        return static::TYPE_ADMIN;
    }

    /**
     * @inheritDoc
     */
    protected function register() : void 
    {
        wp_register_style(self::KEY_CUSTOM_METABOX, Plugin::getURL() . '/vendor/green-everest/wp-plugin-core/src/ressource/css/metabox.css');
    }

    /**
     * @inheritDoc
     */
    protected function load() : void 
    {
        wp_enqueue_style(self::KEY_CUSTOM_METABOX);
    }
}