<?php
namespace WPPluginCore;

defined('ABSPATH') || exit;

use DI\Container;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Web\Abstraction\Endpoint;
use WPPluginCore\Abstraction\IRegisterable;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Util\Date;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\DAO\Abstraction\DAO;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Wordpress\Entity\Metabox;
use WPPluginCore\Service\Wordpress\Entity\Metaboxes;
use WPPluginCore\Service\Wordpress\Entity\PostTypeRegistration;
use WPPluginCore\Service\Wordpress\Ressource\Implementation\JSONAttribute;
use WPPluginCore\Service\Wordpress\Ressource\Implementation\Metabox as MetaboxRessource;

/**
 * The main WPPluginCore class
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class Plugin
{

    /*
     * **************************************************************
     * Plugins section SECTION
     * **************************************************************
     *
     */
    public const MODE_DEBUG = 'debug';
    public const MODE_PROD = 'prod';

    private static string $mode = self::MODE_PROD;

    /**
     * Setts the mode of the Project, if the setted mode is unoknown MODE_PROD is the default value
     *
     * @param string $mode MODE_DEBUG = 'debug' is the debug mode with extended logging and MODE_PROD = 'prod'  is the productive mode with only error and info logging 
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function setMode(string $mode = self::MODE_PROD)
    {
        switch ($mode) {
            case self::MODE_DEBUG:
                self::$mode = self::MODE_DEBUG;
            default:
                self::MODE_PROD;
                break;
        }
    }

    /**
     * Check if the plugin is in Debug mode
     *
     * @return boolean
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function isDebug() : bool
    {
        return self::$mode === self::MODE_DEBUG;
    }

    private function register(array $definitions)
    {
        foreach ($definitions as $definition) {
            $object = $this->container->get($definition);
            if ($object instanceof IRegisterable) {
                $object->registerMe();
            } else {
                throw new IllegalStateException('The class shoul be of registable but the class istn`t: ' . $object);
            }   
        }
    }


    /*
     * **************************************************************
     * Private Plugin SECTION
     * **************************************************************
     *
     */
    private string $slug = 'test-slug';
    private string $url = __FILE__;
    private string $file = __FILE__;

    private array $services;
    private array $endpoints;

    private Container $container;


    /**
     * The Constructor of the Plugin
     *
     * @param string $file the file path of the plugin 
     * @param string $url the url of the filepath of the plugin
     * @param string $slug an unique identifier to identify the plugin
     * @param array $services the services of the plugin
     * @param array $endpoints the endpoints of the plugin
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function __construct(string $file, string $url, string $slug, array $services, array $endpoints,Container $container) 
    {
        $this->slug = $slug;
        $this->file = $file;
        $this->url = $url;

        $this->services = $services;
        $this->endpoints = $endpoints;

        $this->container = $container;
    }

    /**
     * Runs the funciton by register the services and endpoints
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    final public function run() : void
    {
        $this->register($this->services);
        $this->register($this->endpoints);
    }

} 
