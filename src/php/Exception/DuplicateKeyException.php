<?php


namespace WPPluginCore\Exception;

use Throwable;

class DuplicateKeyException extends IllegalArgumentException
{
    public function __construct($message = "Key is already in use", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
