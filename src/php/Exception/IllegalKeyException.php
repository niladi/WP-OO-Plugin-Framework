<?php


namespace WPPluginCore\Exception;
use Throwable;

defined('ABSPATH') || exit;
class IllegalKeyException extends IllegalArgumentException
{
    public function __construct($message = "the used key is not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
