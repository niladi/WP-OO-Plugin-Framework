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

    private MetaboxRessource $metabox;
    private JSONAttribute $jSONAttribute;
    private MetaboxWrapper $metaboxWrapper;


    public function __construct(MetaboxRessource $metabox, JSONAttribute $jSONAttribute, MetaboxWrapper $metaboxWrapper)
    {
        $this->metabox = $metabox;
        $this->jSONAttribute = $jSONAttribute;
        $this->metaboxWrapper = $metaboxWrapper;
    }


    protected function loadAssets(): void
    {
        $this->metabox->loadRessource();
        $this->jSONAttribute->loadRessource();
    }

    protected function showMe() : void
    {
        global $viewParams; // todo cleaner 
        wp_nonce_field( "{$this->metaboxWrapper->slug}_save_meta_box_data", "{$this->metaboxWrapper->slug}_meta_box_nonce");
        echo "<table class='form-table'><tbody>{$this->metaboxWrapper->html}</tbody></table>";
    }

} 