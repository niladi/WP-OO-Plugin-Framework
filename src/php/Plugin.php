<?php
namespace WPPluginCore;

use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Web\Abstraction\Endpoint;
use WPPluginCore\Abstraction\IRegisterable;
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

    private static string $slug = 'test-slug';
    private static string $url = __FILE__;
    private static string $file = __FILE__;
    private static bool $isDebug = false;

    private array $services;
    private array $endpoints;


    public function __construct(string $file, string $url, string $slug, bool $isDebug, array $services, array $endpoints) 
    {
        self::$slug = $slug;
        self::$file = $file;
        self::$url = $url;
        self::$isDebug = $isDebug;

        $this->services = array(
            Date::class,
            Metabox::class,
            PostTypeRegistration::class,
            Save::class,
            JSONAttribute::class,
            MetaboxRessource::class,
            ...$services
        );
        $this->endpoints = $endpoints;
    }

    final public static function buildKey(string $key) : string
    {
        return self::$slug. '-' . $key;
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

    public static function getFile() : string 
    {
        return self::$file;
    }

    public static function isDebug() : bool
    {
        return self::$isDebug;
    }

    public static function getURL() : string
    {
        return self::$url;
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

} 
