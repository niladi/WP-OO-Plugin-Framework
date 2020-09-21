<?php
namespace WPPluginCore;

defined('ABSPATH') || exit;

use DI\Container;
use DI\ContainerBuilder;
use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Web\Abstraction\Endpoint;
use WPPluginCore\Abstraction\IRegisterable;
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
class PluginBuilder 
{

    private array $services;
    private array $endpoints;

    private string $file;
    private string $url;
    private string $slug;

    public function setServices(array $services) : self
    {
        $this->services = $services;
        return $this;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }

    public function setURL(string $url)  : self
    {
        $this->url = $url;
        return $this;
    }
    

    final public function build() : Plugin
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions();
        $containerBuilder->useAutowiring(false);
        $containerBuilder->useAnnotations(false);
        $containerBuilder->addDefinitions($this->services);
        $containerBuilder->addDefinitions($this->endpoints);

        return new Plugin(
            $this->file, 
            $this->url, 
            $this->slug, 
            array_keys($this->services), 
            array_keys($this->endpoints), 
            $containerBuilder->build()
        );
    }

} 
