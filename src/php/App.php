<?php
namespace WPPluginCore;

use WPPluginCore\DBInit;
use WPPluginCore\Abstraction\IRegisterable;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Service\Implementation\Date;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Service\Wordpress\Entity\Metaboxes;
use WPPluginCore\Service\Wordpress\Entity\PostTypeRegistration;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Web\Abstraction\Endpoint;

/**
 * The main WPPluginCore class
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class App 
{
    public const KEY_FILE = 'file';
    public const KEY_URL = 'url';
    public const KEY_IS_DEBUG = 'is_debug';

    private static string $slug;

    private array $services;
    private array $endpoints;

    public function __construct(string $file, string $url, string $slug, bool $isDebug) 
    {
        self::$slug = $slug;
        define(self::$slug. '-'. self::KEY_FILE, $file);
        define(self::$slug. '-'. self::KEY_URL, $url);
        define(self::$slug. '-'.self::KEY_IS_DEBUG, $isDebug);

        $this->services = array();
        $this->endpoints = array();

        $this
            ->addService(Date::class)
            ->addService(Metaboxes::class)
            ->addService(PostTypeRegistration::class)
            ->addService(Save::class);
    }

    final public static function getConstant(string $key)
    {
        return constant(self::$slug. '-' . $key);
    }

    final public static function getDefined(string $key) : bool
    {
        return defined(self::$slug. '-' . $key);
    }

    final public function run() : void
    {
        Logger::registerMe();
        $this->register($this->services);
        DBInit::getInstance()->initDB();
        $this->register($this->endpoints);
    }

    public static function getSlug() : string
    {
        return self::$slug;
    }


    private function register(array $classes)
    {
        foreach ($classes as $class) {
            if (is_subclass_of($class, IRegisterable::class)) {
                $class::registerMe();
            } else {
                throw new IllegalStateException('The class shoul be of registable but the class istn`t: ' . $class);
            }   
        }
    }

    public function addService(string $class) : self
    {
        if (is_subclass_of($class, Service::class)) {
            array_push($this->services,$class );
        } else {
            throw new IllegalStateException('The class shoul be of type Service but the class istn`t: ' . $class);
        }   
        return $this;
    }

    public function addEndpoint(string $class) : self
    {
        if (is_subclass_of($class, Endpoint::class)) {
            array_push($this->services,$class );
        } else {
            throw new IllegalStateException('The class shoul be of type Service but the class istn`t: ' . $class);
        } 
        return $this;  
    }

} 
