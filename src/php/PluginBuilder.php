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
class PluginBuilder 
{
    public const KEY_FILE = 'file';
    public const KEY_URL = 'url';
    public const KEY_IS_DEBUG = 'is_debug';

    private array $services;
    private array $endpoints;

    private string $file;
    private string $url;
    private string $slug;
    private bool $isDebug;

    private EntityFactory $entityFactory;

    public function __construct(string $file, string $url, string $slug, bool $isDebug) 
    {
        $this->services = array();
        $this->endpoints = array();

        $this->file = $file;
        $this->url = $url;
        $this->slug = $slug;
        $this->isDebug = $isDebug;
        
        $this->entityFactory = EntityFactory::getInstance();
    }

    

    final public function build() : Plugin
    {
        return new Plugin($this->file, $this->url, $this->slug, $this->isDebug, $this->services, $this->endpoints);
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

    public function addEntity(string $entityClass, Entity $entityDAO) : self
    {
        $this->entityFactory->addEntity($entityClass, $entityDAO);
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
