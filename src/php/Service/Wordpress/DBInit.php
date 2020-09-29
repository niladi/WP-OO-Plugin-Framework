<?php


namespace WPPluginCore\Service\Wordpress;

use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\Exception\QueryException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Abstraction\RegisterableFactory;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\DB\DBConnector;
use WPPluginCore\Domain\Entity\Abstraction\Entity;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\DAO\Entity\Container\EntityContainer;
use WPPluginCore\Persistence\DB\DBInit as DBDBInit;

defined('ABSPATH') || exit;

/**
 * The Service for the databse setup and destrcut
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class DBInit extends Service
{
    
    private string $pluginFile;

    private DBDBInit $dBInit;
    

    /**
     * DBInit constructor. should be called on init action
     * 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    protected function __construct(LoggerInterface $logger, DBDBInit $dBInit, string $pluginFile)
    {
        parent::__construct($logger);
        $this->dBInit = $dBInit;
        $this->pluginFile = $pluginFile;
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void
    {
        register_activation_hook($this->pluginFile, array($this->dBInit , 'initDB'));
    }
}
