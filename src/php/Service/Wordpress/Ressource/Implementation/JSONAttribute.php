<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Implementation;

use WPPluginCore\Plugin;
use WPPluginCore\Service\Wordpress\Ressource\Abstraction\Ressource;

defined('ABSPATH') || exit;

class JSONAttribute extends Ressource
{

    public const KEY_JSON_ATTRIBUTE = 'json-attribute';

    /**
     * @inheritDoc
     */
     public function register() : void 
    {
        wp_register_script(self::KEY_JSON_ATTRIBUTE, static::getRessourceURLPath() . 'js/json-attribute.js', array( 'jquery' ));
        wp_localize_script(self::KEY_JSON_ATTRIBUTE, 'acme_ajax_object', array(
            'ajaxurl'  => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('acme-security-nonce')
        ));
    }

    /**
     * @inheritDoc
     */
    protected function load() : void 
    {
        wp_enqueue_script(self::KEY_JSON_ATTRIBUTE);
    }
}