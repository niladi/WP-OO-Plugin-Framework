<?php


namespace WPPluginCore;

use Monolog\Handler\ChromePHPHandler;
use WPPluginCore\Plugin;
use PhpConsole\Connector;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\PHPConsoleHandler;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Abstraction\RegisterableFactory;
use WPPluginCore\Exception\IllegalStateException;

defined('ABSPATH') || exit;

/**
 * The Singleton logger for the plugin
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class Logger extends RegisterableFactory
{
    private static ?self $instance = null;

    protected MonologLogger $logger;

    private static string $file;

    private bool $isDebug;


    /**
     * @inheritDoc
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct()
    {
        static::$file = dirname(Plugin::getFile());

        $this->logger = new MonologLogger('licence_sales_logger');

        $this->isDebug = Plugin::isDebug();

        if ($this->isDebug) {
            $chromeconsole = new ChromePHPHandler(MonologLogger::DEBUG);
            $this->logger->pushHandler($chromeconsole);
            $stream = new StreamHandler(static::$file . '/licence_sales.log', MonologLogger::DEBUG);
        } else {
            $stream = new StreamHandler(static::$file . '/licence_sales.log', MonologLogger::INFO);
        }
        
        $this->logger->pushHandler($stream);

    }

    /**
     * Adds an error message to the logger
     *
     * @param string $message the content of the error message
     * @param array $context the context of the message
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function error(string $message, array $context = []) : void
    {
        static::getLogger()->addRecord(MonologLogger::ERROR, $message, $context);
    } 

    /**
     * Adds an info message to the logger
     *
     * @param string $message the content of the message
     * @param array $context the context of the message
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function info(string $message, array $context = []) : void
    {
        static::getLogger()->addRecord(MonologLogger::INFO, $message, $context);
    }

    public static function debug(string $message, array $context = []) : void
    {
        static::getLogger()->addRecord(MonologLogger::DEBUG, $message, $context);
    }

    private static function getLogger() : MonologLogger
    {
        return static::getInstance()->logger;
    }


    
}
