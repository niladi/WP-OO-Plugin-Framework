<?php

namespace WPPluginCore\View\Implementation;

use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\View\Abstraction\View;


defined('ABSPATH') || exit;

class MetaboxWrapper
{
    public WPEntity $wpEntity;

    public function __construct(WPEntity $wpEntity)
    {
        $this->wpEntity = $wpEntity;
    }
}