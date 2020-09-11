<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Abstraction;

defined('ABSPATH') || exit;
use WPPluginCore\App;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Service\Abstraction\Service;

abstract class Ressource extends Service
{
    protected const TYPE_META_BOX = 1;
    protected const TYPE_ADMIN = 2;
    protected const TYPE_LOAD = 4;

    protected string $assetsPath;
    private bool $ressourceRegistered = false;

    public function __construct()
    {
        parent::__construct();
        if (!defined(App::buildKey(App::KEY_URL))) {
            throw new IllegalStateException('The file path is not set');
        }
        $this->assetsPath =  constant(App::buildKey(App::KEY_URL)) . '/src/ressource';
    }

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
            throw new IllegalStateException('Can\'t load ressource before register it');
        }

        $this->load();
    }

    abstract protected function load()  : void;

    abstract static protected function getType() : int;

    public static function registerMe() : void
    {
        parent::registerMe(); 
        $type = static::getType();
        $callback = array( static::getInstance(), 'registerRessource' );
        if ($type >= 4) {
            add_action('wp_enqueue_scripts', $callback);
        } elseif ($type >= 2) {
            add_action('admin_enqueue_scripts', $callback);
        } elseif ($type >= 1) {
            add_action('add_meta_boxes', $callback);
        }  else {
            throw new IllegalStateException('no valid type for ressource');
        }
    }


}