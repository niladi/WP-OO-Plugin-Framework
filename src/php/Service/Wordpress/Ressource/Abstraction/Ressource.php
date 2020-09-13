<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Abstraction;

defined('ABSPATH') || exit;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Logger;
use WPPluginCore\Service\Abstraction\Service;

abstract class Ressource extends Service
{
    // protected const TYPE_META_BOX = 1;
    protected const TYPE_ADMIN = 1;
    protected const TYPE_LOAD = 2;

    private static array $adminRessources = array();
    private static array $frontRessources = array();
    private bool $ressourceRegistered = false;

    /**
     * Register the ressource
     *
     * @return void
     * @throws IllegalStateException if the resource is already registered
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function registerRessource() : void
    {
        if ($this->ressourceRegistered) {
            throw new IllegalStateException('the reossource is already registered');
        }

        $this->register();
        $this->ressourceRegistered = true;
    }

    abstract protected function register()  : void;

    /**
     * Enques the ressource for using it
     *
     * @return void
     * @throws IllegalStateException if the ressource is not registered
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function loadRessource()  : void
    {
        if ($this->ressourceRegistered === false) {
            Logger::error('Didn\'t register the ressource');
        }

        $this->load();
    }

    abstract protected function load()  : void; 

    abstract static protected function getType() : int;

    static public function registerMe(Plugin $plugin): void 
    {
        parent::registerMe($plugin); 
        if (static::getType() | self::TYPE_ADMIN) {
            array_push(self::$adminRessources, static::getInstance());
        }
        if (static::getType() | self::TYPE_LOAD) {
            array_push(self::$frontRessources, static::getInstance());
        }
        
        add_action('wp_enqueue_scripts', array(self::class, 'registerFrontRessources'));
        add_action('admin_enqueue_scripts', array(self::class, 'registerAdminRessources'));
    }

    public static function registerFrontRessources() : void
    {
        self::registerArray(self::$frontRessources);
    }

    public static function registerAdminRessources(): void
    {
        self::registerArray(self::$adminRessources);
    }

    private static function registerArray(array $arr) : void
    {
        foreach ($arr as $ressource) {
            $ressource->register();
        }
    }

    public static function getRessourceURLPath(string $file = __FILE__)
    {
        return plugins_url( '../../../../../ressource/', $file );
    }

    /*
    * Todo workarround
    *add_action('wp_enqueue_scripts', $callback);
    *add_action('admin_enqueue_scripts', $callback);
    *add_action('add_meta_boxes', $callback);
    */



}