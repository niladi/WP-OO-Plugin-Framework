<?php


namespace WPPluginCore\Util;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

defined('ABSPATH') || exit;


class LoggerFactory
{
    public static function create(string $file, bool $isDebug, string $name) : Logger
    {
        $logger = new Logger($name);

        if ($isDebug) {
            $chromeconsole = new ChromePHPHandler(Logger::DEBUG);
            $logger->pushHandler($chromeconsole);
            $stream = new StreamHandler($file, Logger::DEBUG);
        } else {
            $stream = new StreamHandler($file, Logger::INFO);
        }
        $logger->pushHandler($stream);

        return $logger;
    }
}