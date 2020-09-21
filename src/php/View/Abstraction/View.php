<?php

namespace WPPluginCore\View\Abstraction;

use WPPluginCore\Exception\IllegalStateException;

defined('ABSPATH') || exit;

abstract class View 
{
    //todo add reuqired paramter validation

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
     * @param array $params which holds the specific information fro teh view
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    abstract static protected function showMe() : void;


    /**
     * Loads the ressources which are required for the view
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    protected function loadAssets() : void
    {
        // playholder if there are needed any assets
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