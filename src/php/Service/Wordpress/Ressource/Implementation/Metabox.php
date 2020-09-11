<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Implementation;

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
        return static::TYPE_META_BOX;
    }

    /**
     * @inheritDoc
     */
    protected function register() : void 
    {
        wp_register_style(self::KEY_CUSTOM_METABOX,$this->assetsPath . '/css/metabox.css');
    }

    /**
     * @inheritDoc
     */
    protected function load() : void 
    {
        wp_enqueue_style(self::KEY_CUSTOM_METABOX);
    }
}