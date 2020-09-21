<?php


namespace WPPluginCore\Util;

use DI\Definition\Helper\FactoryDefinitionHelper;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

use function DI\factory;

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

    public static function factory(string $file, bool $isDebug, string $name) : FactoryDefinitionHelper
    {
        return factory(fn(string $file, bool $isDebug, string $name) => self::create($file, $isDebug, $name));
    }
}