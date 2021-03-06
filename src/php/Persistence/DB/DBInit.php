<?php


namespace WPPluginCore\Persistence\DB;

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

defined('ABSPATH') || exit;

/**
 * The Service for the databse setup and destrcut
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class DBInit
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


    /**
     * @var class-string<Entity>[]
     */
    private array $entities;

    private DBConnector $dbConnector;
    
        /**
     * DBInit constructor. should be called on init action
     * 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     * @psalm-param class-string<Entity>[] $entities
     */
    public function __construct(DBConnector $dbConnector, array $entities)
    {
        $this->entities = array();
        foreach($entities  as $entity) {
            if (!is_subclass_of($entity, Entity::class)) {
                throw new IllegalArgumentException('String is not of class');
            }
            array_push($this->entities, $entity);
        }
        $this->onInit = false;
        $this->initDB = false;
        
        $this->dbConnector =  $dbConnector;
    }

    /**
     * Inits the database value
     *
     * @return true if the initlazing went good
     *
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    public function initDB() : bool
    {
        $this->onInit = true;
        if (!$this->initDB) {
            try {
                foreach ($this->entities as $entityClass) {
                    $this->createTable($entityClass::init());
                }
                $this->initDB = true;
            } catch (QueryException $queryException) {
                throw new IllegalStateException('Cant init database', 0, $queryException);
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
            foreach ($this->entities as $entityClass) {
                $this->dropTable($entityClass);
            }
        } catch (QueryException $queryException) {
            throw new IllegalStateException('Cant drop database',0,  $queryException);
        }
        $this->initDB = false;
        return true;
    }
    /**
     * Drops the table of an entity
     *
     * @param class-string<Entity> $entityClass
     * @return void
     * 
     * @throws QueryException if something of the query went wrong
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private function dropTable($entityClass) : void
    {
        $db = $this->dbConnector->getConnection();
        $statement = sprintf('DROP TABLE %s;', $entityClass::getTable());
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
                throw new IllegalStateException('Illegal state occurs in ' . __FILE__ . ' because getAttributesKeys returns an non valid key');
            }
        }
        $s .= sprintf('PRIMARY KEY (%s), ', $entity->getPrimaryKeysSerialized());
        foreach ($entity->getForeignKeys() as $key => $value) {
            $s .= sprintf('FOREIGN KEY (%s) REFERENCES %s, ', $key, $value);
        }
        return rtrim($s, ', ');
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
