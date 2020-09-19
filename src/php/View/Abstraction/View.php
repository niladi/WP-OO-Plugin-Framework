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
    static function show() : void
    {
        if (static::validateParams()) {
            static::loadAssets();
            static::showMe();
        } else {
            throw new IllegalStateException("Not all params are set");
        }
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
    static protected function loadAssets() : void
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