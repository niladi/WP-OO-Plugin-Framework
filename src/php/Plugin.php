<?php
namespace WPPluginCore;

defined('ABSPATH') || exit;

use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Web\Abstraction\Endpoint;
use WPPluginCore\Abstraction\IRegisterable;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Service\Implementation\Date;
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

    private static $coreRegistered = false;

    /**
     * @var <string, Plugin[]>
     */
    private static array $plugins = array();

    /**
     * Adds a new Plugin the the plugins
     *
     * @param Plugin $plugin the new plugin
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function add(Plugin $plugin) : void
    {
        self::$plugins[$plugin->getSlug()] = $plugin;
    }

    /**
     * Returns the Plugin by its slug
     *
     * @param string $slug the unique slug of the plugin
     * @return Plugin 
     * @throws IllegalKeyException if the slug is not added
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function get(string $slug ) : Plugin
    {
        if (array_key_exists($slug, self::$plugins)) {
            return self::$plugins[$slug];
        } 
        throw new IllegalKeyException();
    }

    /**
     * Returns the first plugin
     *
     * @return Plugin
     * @throws IllegalStateException if there is no plugin registerd
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function getFirst() : Plugin
    {
        if (empty(self::$plugins)) {
            throw new IllegalStateException('There is no plugin registerd');
        }
        return self::$plugins[array_key_first(self::$plugins)];
    }

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

    private static function registerCore() : void
    {
        if (! self::$coreRegistered) {
            self::register(array (
                Logger::class,
                Date::class,
                Metabox::class,
                PostTypeRegistration::class,
                Save::class,
                JSONAttribute::class,
                MetaboxRessource::class,
            ));
            self::$coreRegistered = true;
        }
    }

    private static function register(array $classes)
    {
        foreach ($classes as $class) {
            if (is_subclass_of($class, IRegisterable::class)) {
                $class::registerMe();
            } else {
                throw new IllegalStateException('The class shoul be of registable but the class istn`t: ' . $class);
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
    public function __construct(string $file, string $url, string $slug, array $services, array $endpoints) 
    {
        $this->$slug = $slug;
        $this->$file = $file;
        $this->$url = $url;

        $this->services = array(
            ...$services
        );
        $this->endpoints = $endpoints;
    }

    /**
     * Contacts the plugin slug with the specific $key, with '-' as delimeter
     *
     * @param string $key the key suffix
     * @return string the full key
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    final public function buildKey(string $key) : string
    {
        return $this->slug. '-' . $key;
    }

    /**
     * Runs the funciton by register the services and endpoints
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    final public function run() : void
    {
        self::registerCore();
        self::register($this->services);
        DBInit::registerMe($this->file);
        self::register($this->endpoints);
    }

    /**
     * Returns the slug of the key
     *
     * @return string
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * Returns the file of teh plugin
     *
     * @return string
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getFile() : string 
    {
        return $this->file;
    }

    /**
     * returns the url of the sring
     *
     * @return string
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getURL() : string
    {
        return $this->url;
    }

} 
