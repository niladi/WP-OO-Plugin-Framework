<?php

namespace WPPluginCore\Persistence\Domain\Entity\Abstraction;

defined('ABSPATH') || exit;

use WPPluginCore\Logger;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation\WPPostID;

abstract class WPEntity extends Entity
{

    /*
     * **************************************************************
     * Variables SECTION
     * **************************************************************
     *
     */


    /**
     * Key for notes attribute
     */
    public const KEY_WP_POST_ID = 'wp_post_id';

    /*
     * **************************************************************
     * Wordpress Post SECTION
     * **************************************************************
     *
     */

    /**
     * returns the slug
     *
     * @return String returns the Slug
     */
    abstract public static function getSlug(): String;


    /**
     * returns the labels of the post type
     *
     * @return array the labels
     */
    abstract public static function getLabels() : array;


    /**
     * Registers the Post Type with Default entries and default save and metabox hook
     *
     * @param $singular String singular name of the post type
     * @param $plural String plural name of the post typ
     *
     * @return array the labels
     */
    final protected static function getDefaultLabels(string $singular, string $plural) : array
    {
        // echo '<script>alert("'.static::getSlug() .'")</script>';
        return array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => __($plural, 'wp-plugin-core'),
            'name_admin_bar'        => __($plural, 'wp-plugin-core'),
            'parent_item_colon'     => __($singular, 'wp-plugin-core') . ' ' . __('item', 'wp-plugin-core'),
            'all_items'             => __($plural, 'wp-plugin-core'),
            'add_new_item'          => __($singular, 'wp-plugin-core') . ' ' . __('hinzufügen', 'wp-plugin-core'),
            'add_new'               => __('Hinzufügen', 'wp-plugin-core'),
            'new_item'              => __('Neue', 'wp-plugin-core') . ' ' . __($singular, 'wp-plugin-core'),
            'edit_item'             => __($singular, 'wp-plugin-core') . ' ' . __('Bearbeiten', 'wp-plugin-core'),
            'update_item'           => __($singular, 'wp-plugin-core') . ' ' . __('Aktualisieren', 'wp-plugin-core'),
            'view_item'             => __($singular, 'wp-plugin-core') . ' ' . __('Anzeigen', 'wp-plugin-core'),
            'search_items'          => __($singular, 'wp-plugin-core') . ' ' . __('Suchen', 'wp-plugin-core'),
            'not_found'             => __('Nicht Gefunden', 'wp-plugin-core'),
            'not_found_in_trash'    => __('Nicht Gefunden im Papierkorb', 'wp-plugin-core'),
            'items_list'            => __($singular, 'wp-plugin-core') . ' ' . __('Listen', 'wp-plugin-core'),
            'items_list_navigation' => __($plural, 'wp-plugin-core') . ' ' . __('Navigieren', 'wp-plugin-core'),
            'filter_items_list'     => __('Filter', 'wp-plugin-core') . ' ' . __($plural, 'wp-plugin-core'),
        );
    }

    /**
     * @inheritDoc
     */
    public function addAttributes(): void
    {
        parent::addAttributes();
        $this->addAttribute(new WPPostID(self::KEY_WP_POST_ID, __('Wordpress Post ID', 'wp-plugin-core'), static::getSlug()));
    }

    /**
     * Returns the wordpress  post Title
     *
     * @return string
     */
    abstract public function getWPPostTitle(): string;


    public function getWPPostID()
    {
        try {
            return $this->getAttributeValue(self::KEY_WP_POST_ID);
        } catch (IllegalKeyException $e) {
            Logger::error('There is no way an IllegalKeyException is throwed');
            exit;
        }
    }

    abstract static public function getMenuSlug() : string;
}
