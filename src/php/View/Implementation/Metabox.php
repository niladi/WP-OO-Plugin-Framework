<?php

namespace WPPluginCore\View\Implementation;

use WPPluginCore\View\Abstraction\View;
use WPPluginCore\Service\Wordpress\Assets\Implementation\PDF;
use WPPluginCore\Service\Wordpress\Assets\Implementation\Papaparse;
use WPPluginCore\Service\Wordpress\Assets\Implementation\CustomAdmin;
use WPPluginCore\Service\Wordpress\Ressource\Implementation\Metabox as MetaboxRessource;
use WPPluginCore\Service\Wordpress\Ressource\Implementation\JSONAttribute;

defined('ABSPATH') || exit;

class Metabox extends View
{
    public const SLUG = 'slug';
    public const HTML = 'html';


    protected static function loadAssets(): void
    {
        MetaboxRessource::getInstance()->loadRessource();
        JSONAttribute::getInstance()->loadRessource();
    }

    protected static function showMe(array $params = array()) : void
    {
        wp_nonce_field( $params[self::SLUG] . '_save_meta_box_data', $params[self::SLUG] . '_meta_box_nonce');
        echo '<table class="form-table"><tbody>' . $params[self::HTML] . '</tbody></table>';
    }
} 