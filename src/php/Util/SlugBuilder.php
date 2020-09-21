<?php


namespace WPPluginCore\Util;
defined('ABSPATH') || exit;

/**
 * Util class for consistent slugs/keys
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class SlugBuilder
{
    public static function buildSlug(string $pluginSlug, string $key, string $type = '') : string
    {
        $t = ($type != '') ? $type . '-' : '';
        return "{$pluginSlug}-{$t}-{$key}";
    }
}