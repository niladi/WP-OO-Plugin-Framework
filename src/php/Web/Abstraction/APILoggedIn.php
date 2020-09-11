<?php


namespace WPPluginCore\Web\Abstraction;
defined('ABSPATH') || exit;
use WP_REST_Request;
use WPPluginCore\Service\Domain\LTURelation;

abstract class APILoggedIn extends API
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
        return is_user_logged_in();
    }
}
