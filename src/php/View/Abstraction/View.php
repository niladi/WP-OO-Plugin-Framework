<?php

namespace WPPluginCore\View\Abstraction;

use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Service\Wordpress\Ressource\Abstraction\Ressource;

defined('ABSPATH') || exit;

abstract class View 
{
    //todo add reuqired paramter validation

    /**
     * @var Ressource[]
     */
    private array $assets;
    
    public function __construct()
    {
        $this->assets = array();
    }

    protected function addAsset(Ressource $asset): void 
    {
        array_push($this->assets, $asset);
    }

    /**
     * Loads the ressources and shows the view
     *
     * @param array $params the params which are requered to display the vie
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    function show() : void
    {
        $this->loadAssets();
        $this->showMe();
    }

    /**
     * echo the HTML for the view
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    abstract protected function showMe() : void;


    /**
     * Loads the ressources which are required for the view
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private function loadAssets() : void
    {
        foreach($this->assets as $asset) {
            $asset->loadRessource();
        }
    }
}