<?php


namespace WPPluginCore\Exception;
use Throwable;

defined('ABSPATH') || exit;
class IllegalKeyException extends IllegalArgumentException
{
    public function __construct(string $message = "the used key is not found",int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
