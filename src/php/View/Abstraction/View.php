<?php

namespace WPPluginCore\View\Abstraction;

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
    static function show(array $params = array()) : void
    {
        static::loadAssets();
        static::showMe($params);
    }

    /**
     * echo the HTML for the view
     *
     * @param array $params which holds the specific information fro teh view
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    abstract static protected function showMe(array $params = array()) : void;


    /**
     * Loads the ressources which are required for the view
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    abstract static protected function loadAssets() : void;
}