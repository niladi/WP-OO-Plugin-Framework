<?php
namespace WPPluginCore;

defined('ABSPATH') || exit;

use DI\Container;
use DI\ContainerBuilder;


/**
 * The main WPPluginCore class
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class PluginBuilder 
{
    private array $helper;
    private array $daos;
    private array $services;
    private array $endpoints;

    private string $file;
    private string $url;
    private string $slug;
    
    public function __construct(string $file,  string $url, string $slug)
    {
        $this->file = $file;
        $this->url = $url;
        $this->slug = $slug;
    }

    public function setServices(array $services) : self
    {
        $this->services = $services;
        return $this;
    }

    public function setEndpoints(array $endpoints) : self
    {
        $this->endpoints = $endpoints;
        return $this;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setHelper(array $helper) : self
    {
        $this->helper = $helper;
        return $this;
    }

    public function setDAOs(array $daos) : self
    {
        $this->daos = $daos;
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
        $containerBuilder->useAutowiring(true);
        $containerBuilder->useAnnotations(false);

        $containerBuilder->addDefinitions($this->helper);
        $containerBuilder->addDefinitions($this->daos);
        $containerBuilder->addDefinitions($this->services);
        $containerBuilder->addDefinitions($this->endpoints);

        $containerBuilder->addDefinitions(['plugin.file' => $this->file, 'plugin.slug' => $this->slug, 'plugin.url' => $this->url]);

        // if (/* is production */) { todo only in production
            $containerBuilder->enableCompilation(dirname($this->file) . '/cache');
        // } 
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
