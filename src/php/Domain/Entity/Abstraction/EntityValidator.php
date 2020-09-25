<?php

namespace WPPluginCore\Domain\Entity\Abstraction;

defined('ABSPATH') || exit;

interface EntityValidator 
{
    public function isValid(Entity $entity) : bool;
}