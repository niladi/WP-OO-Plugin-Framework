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
    
    public function __construct(... $assets)
    {
        $this->assets = $assets;
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
            if ($asset instanceof Ressource) {
                $asset->loadRessource();
            } else {
                throw new IllegalArgumentException("wraong type of " . $asset::class);    
            }
        }
    }

    /**
     * Validates the view params
     *
     * @return boolean
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    static protected function validateParams() : bool
    {
        return true;
    }
}