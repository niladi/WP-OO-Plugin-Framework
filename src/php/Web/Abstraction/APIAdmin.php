<?php


namespace WPPluginCore\Web\Abstraction;
defined('ABSPATH') || exit;
use WP_REST_Request;


abstract class APIAdmin extends API
{
    /**
     * Wichtig das diese Funktioniert so muss .htaacces der auth header zugellassen sein
     *
     * @param WP_REST_Request $request
     *
     * @return bool
     */
    public function permission(WP_REST_Request $request)
    {
        return current_user_can('administrator');
    }
}
