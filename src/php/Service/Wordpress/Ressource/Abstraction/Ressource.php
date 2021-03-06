<?php

namespace WPPluginCore\Service\Wordpress\Ressource\Abstraction;

defined('ABSPATH') || exit;
use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalStateException;

abstract class Ressource extends Service
{
    public const TYPE_ADMIN = 1;
    public const TYPE_LOAD = 2;

    protected string $pluginURL;
    private int $type;

    private bool $ressourceRegistered;

    public function __construct(LoggerInterface $logger, string $pluginURL, int $type)
    {
        parent::__construct($logger);
        $this->pluginURL = $pluginURL;
        $this->type = $type;
        $this->ressourceRegistered = false;
    }

    public function registerRessource() : void
    {
        if (!$this->ressourceRegistered) {
            $this->register();
            $this->ressourceRegistered = true;
        }
        
    }

    abstract protected function register() : void;

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
            throw new IllegalStateException('Didn\'t register the ressource');
        }

        $this->load();
    }

    abstract protected function load()  : void; 

    public function registerMe() : void 
    {
        parent::registerMe(); 
        if ($this->type | self::TYPE_ADMIN) {
            add_action('admin_enqueue_scripts', array($this, 'registerRessource'));
        }
        if ($this->type | self::TYPE_LOAD) {
            add_action('wp_enqueue_scripts', array($this, 'registerRessource'));
        }
        
    }

    public static function getRessourceURLPath(string $file = __FILE__): string
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