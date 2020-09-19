<?php


namespace WPPluginCore\Service\Wordpress;
defined('ABSPATH') || exit;

use WPPluginCore\Plugin;
use WPPluginCore\Service\Abstraction\Service;


class NotificationWrapper extends Service
{

    private static array $plugins = array();

    public static function registerMe(Plugin $plugin) : void
    {
        array_push(self::$plugins, $plugin->getSlug());

    }

    public function pushPersistent() 
    {

    }

    public function pushTemporary()
    {
        
    }
}