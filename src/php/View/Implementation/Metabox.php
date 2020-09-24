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
        parent::__construct();
        $this->addAsset($metabox);
        $this->addAsset($jSONAttribute);
        $this->metaboxWrapper = $metaboxWrapper;
    }

    protected function showMe() : void
    {
        $slug = $this->metaboxWrapper->wpEntity::getSlug();
        $html = $this->createHTML();
        wp_nonce_field( "{$slug}_save_meta_box_data", "{$slug}_meta_box_nonce");
        echo "<table class='form-table'><tbody>{$html}</tbody></table>";
    }

    protected function createHTML() : string
    {
        $output = '';
        $entity = $this->metaboxWrapper->wpEntity;
        foreach ($entity->getAttributes() as $key => $value) {
            $output .= $value->getAdminHTML();
        }
        return $output;
    }

} 