<?php

namespace WPPluginCore\View\Implementation;

use WPPluginCore\View\Abstraction\View;


defined('ABSPATH') || exit;

class Notification extends View
{
    public const TITLE = 'title';
    public const MESSAGE = 'message';
    public const LEVEL = 'level';

    public function showMe() : void
    {
        global $viewParams;
        echo "<div class='notice {$viewParams[self::LEVEL]} is-dismissible'><p><strong>{$viewParams[self::TITLE]}</strong></p><p>{$viewParams[self::MESSAGE]}</p></div>";
    }

    protected function validateParams(): bool
    {
        global $viewParams;
        return isset($viewParams[self::TITLE]) && isset($viewParams[self::MESSAGE]) && isset($viewParams[self::LEVEL]);
    }


}