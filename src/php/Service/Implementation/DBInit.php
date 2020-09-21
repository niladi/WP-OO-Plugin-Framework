<?php


namespace WPPluginCore;

use Psr\Log\LoggerInterface;
use WPPluginCore\Logger;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\QueryException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Abstraction\RegisterableFactory;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\DAO\Adapter\DBConnector;
use WPPluginCore\Persistence\DAO\Entity\EntityContainer;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity;

defined('ABSPATH') || exit;

/**
 * The Service for the databse setup and destrcut
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class DBInit extends Service
{
    /**
     * if inti db already executed
     * 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    private bool $initDB;

    /**
     * while the the dabase is initialized
     * 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    private bool $onInit;

    
    private string $pluginFile;

    private EntityContainer $entityContainer;

    private DBConnector $dbConnector;
    

    /**
     * DBInit constructor. should be called on init action
     * 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    protected function __construct(LoggerInterface $logger, EntityContainer $entityContainer, DBConnector $dbConnector, string $pluginFile)
    {
        parent::__construct($logger);
        $this->onInit = false;
        $this->initDB = false;
        $this->pluginFile = $pluginFile;
        $this->entityContainer = $entityContainer;
        $this->dbConnector =  $dbConnector;
    }

    /**
     * Inits the database value
     * 
     * @return bool if the initlazing went good
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    public function initDB() : bool
    {
        $this->onInit = true;
        if (!$this->initDB) {
            try {
                foreach ($this->entityContainer->getAll() as $dao) {
                    $this->createTable($dao->getEntityFactory()->entity());
                }
                $this->initDB = true;
            } catch (QueryException $queryException) {
                $this->logger->error('Cant init database', $queryException->getTrace());
                wp_die( __('Die Website ist wegen technischer schwierigkeiten im Moment nicht erreichbar', 'wp-plugin-core'));
            }

        }
        $this->onInit = false;
        return true;
    }

    /**
     * Creates the table for instances from the class of $entity
     *
     * @param Entity $entity could be an empty instance
     * 
     * @throws QueryException if something of the query went wrong
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    private function createTable(Entity $entity) : void
    {
        $db = $this->dbConnector->getConnection();
        $statement = sprintf('CREATE TABLE IF NOT EXISTS %s (%s);', $entity::getTable(), $this->getAttributes($entity));
        $db->exec($statement);
    }

    /**
     * Drops each table, (should only implemented for test purposes)
     *
     * @return bool if something went wrong it returns false
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function dropDB() : bool
    {
        try {
            foreach ($this->entityContainer->getAll() as $entity) {
                $this->dropTable($entity->getEntityFactory()->entity());
            }
        } catch (QueryException $queryException) {
            $this->logger->error('Cant drop database', $queryException->getTrace());
            return false;
        }
        $this->initDB = false;
        return true;
    }

    /**
     * Drops the table of an entity
     *
     * @param Entity $entity
     * @return void
     * 
     * @throws QueryException if something of the query went wrong
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private function dropTable(Entity $entity) : void
    {
        $db = $this->dbConnector->getConnection();
        $statement = sprintf('DROP TABLE %s;', $entity::getTable());
        $db->exec($statement);
    }

    /**
     * Helper function to return all attributes from an Enity
     *
     * @param Entity $entity
     *
     * @return string
     */
    private function getAttributes(Entity $entity) : string
    {
        $s = '';
        foreach ($entity->getAttributesKeys(false) as $key) {
            try {
                $s .= sprintf('%s %s, ', $key, $entity->getAttribute($key)->getDBSetup());
            } catch (IllegalKeyException $e) {
                $this->logger->error('Illegal state occurs in ' . __FILE__ . ' because getAttributesKeys returns an non valid key');
            }
        }
        $s .= sprintf('PRIMARY KEY (%s), ', $entity->getPrimaryKeysSerialized());
        foreach ($entity->getForeignKeys() as $key => $value) {
            $s .= sprintf('FOREIGN KEY (%s) REFERENCES %s, ', $key, $value);
        }
        return rtrim($s, ', ');
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void
    {
        register_activation_hook($this->pluginFile, array($this , 'initDB'));
    }

    /**
     * @return bool returns true if the DB is on init
     */
    public function onInit() : bool
    {
        return $this->onInit;
    }

    /**
     * returns the boolean value if the db is initalized
     *
     * @return boolean
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function isInitialized() : bool 
    {
        return $this->initDB;
    }
}
