<?php

namespace WPPluginCore\Persistence\DAO\Entity\Abstraction;

defined('ABSPATH') || exit;

use FactoryClass;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use WPPluginCore\Util\Parser;
use WPPluginCore\Logger;
use WPPluginCore\Persistence\Domain;
use WPPluginCore\Exception\ReadException;
use WPPluginCore\Exception\QueryException;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Exception\DeleteException;
use WPPluginCore\Exception\ParserException;
use WPPluginCore\Exception\UpdateException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\NegativIdException;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Exception\IllegalValueException;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\DAO\Adapter\DBConnector;
use WPPluginCore\Persistence\DAO\Entity\Container\EntityContainer;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity as DomainEntity;


/**
 * 
 * @author
 */
abstract class Entity 
{

    protected EntityFactory $entityFactory;
    protected DBConnector $dbConnector;
    protected LoggerInterface $logger;

    public function __construct(EntityFactory $entityFactory, EntityContainer $entityContainer, DBConnector $dbConnector, LoggerInterface $logger)
    {
        $this->entityFactory = $entityFactory;
        $this->dbConnector = $dbConnector;
        $this->logger = $logger;
        $entityContainer->set($entityFactory->getEntityClass()::getTable(), $this);
    }

    public function getEntityFactory() : EntityFactory 
    {
        return $this->entityFactory;
    }

    /**
     * Instanciate from DB (should only executed from database, because no errors are thrown)
     *
     * @param array $metaarr 
     */
    final private function instanceFromDB(array $metaarr)  : DomainEntity
    {
        try {
            return ( $this->entityFactory->entity($metaarr));
        } catch (IllegalArgumentException $e) {
            $this->logger->error('Database entry is corrupted: ' . $e->getMessage(), $metaarr);
        }
    }

    /**
     * Create a new Entry of an Enityt
     *
     * @param DomainEntity $entity
     *
     * @return bool true if everything is
     *
     */
    public function create(DomainEntity $entity) : bool
    {
        $arr = $entity->getAttributesValuesAssoc();
        unset($arr[DomainEntity::KEY_ID]);
        try {
            $this->dbConnector->exec(
                "INSERT INTO {$entity::getTable()} ({implode(', ', array_keys($arr))}) VALUES ({implode(', ', array_values($arr))}})",
            );
        } catch (QueryException $exception) {
            $this->logger->error('Can\'t create an entity: Error Message: ' . $exception->getMessage(), $arr);
            return false;
        }

        try {
            $id = Parser::strToInt($this->dbConnector->getConnection()->lastInsertId());
            $entity->setID($id);
        } catch (IllegalValueException|ParserException $e) {
            $this->logger->error('The Value of the int is corrupted ' . $id, $e->getTrace());
            die;
        }


        return true;
    }

    /**
     * Create an new Entry of PostType
     *
     * @param array $metaarr
     *
     * @return bool
     * @throws AttributeException
     * @throws WPDAOException if something went wrong
     */
    public function createByArray(array $metaarr) : bool
    {
        return $this->create($this->entityFactory->entity( $metaarr));
    }

    /**
     *
     * Read an entity of the Database by its id
     *
     * @param int $id the entity which should deleted
     *
     * @return DomainEntity
     */
    public function read(int $id) : ?DomainEntity
    {
        try {
            return $this->crudValidation($id)->readSingleByKeyValue(DomainEntity::KEY_ID, $id);
        } catch (NegativIdException $negativeIdException) {
            return null;
        }
    }
    /**
     * @param DomainEntity $entity
     * @param array $keys
     *
     * @return DomainEntity
     */
    public function readSingleByEntityKeys(DomainEntity $entity, array $keys) : ?DomainEntity
    {
        return $this->querySingle("SELECT * FROM {$entity::getTable()} WHERE {$entity->attributesForDB($keys, '=', ' AND ')}");
    }

    /**
     * @param array $arr
     * @param bool $single
     *
     * @return DomainEntity|null
     * @throws AttributeException
     * @throws QueryException
     */
    public function readSingleByArray(array $arr) : ?DomainEntity
    {
        return $this->readSingleByEntityKeys($this->entityFactory->entity($arr), array_keys($arr));
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return DomainEntity|null
     * @throws QueryException
     */
    public function readSingleByKeyValue(string $key, $value) : ?DomainEntity
    {
        return $this->readSingleByArray(array($key => $value));
    }


    /**
     * Returns the entities filtered by the values from the key at the enitty
     *
     * @param DomainEntity $entity the enity from which the values should be getted
     * @param array $keys the keys that specifies the filter
     *
     * @return DomainEntity[] the whole entities or null if nothing is found
     * @throws IllegalKeyException if the is one unvalid key
     * @throws QueryException if an error occurs on the query
     */
    public function readMultipleByEntityKeys(DomainEntity $entity, array $keys) : array
    {
        return $this->queryMultiple(
            "SELECT * FROM {$entity::getTable()} WHERE { $entity->attributesForDB($keys, '=', ' AND ')}");
    }

    /**
     * Returns the Entities
     *
     * @param array $arr
     *
     * @return DomainEntity[]
     * @throws QueryException
     */
    public function readMultipleByArray(array $arr) : array
    {
        return $this->readMultipleByEntityKeys($this->entityFactory->entity($arr), array_keys($arr));
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return DomainEntity[]
     *
     */
    public function readMultipleByKeyValue(string $key, $value) : array
    {
        return $this->readMultipleByArray(array($key => $value));
    }



    /**
     * Updates an entity in the database
     *
     * @param DomainEntity $entity the updated entity
     *
     * @throws IllegalArgumentException if something went wrong
     */
    public function update(DomainEntity $entity): void
    {
        try {
            $this->crudValidation($entity->getID());
            $query = $entity->attributesForDB($entity->getAttributesKeys());
        } catch (NegativIdException $ex) {
            throw new IllegalArgumentException($ex->getMessage());
        }
        $entity->validate();
        try {
        $this->getConnector()->exec("UPDATE {$entity::getTable()} SET {$query} WHERE id={$entity->getID()}", false);
        } catch (QueryException $ex) {
            throw new IllegalArgumentException("Watchout: beforeSave was executed \n" . $ex->getMessage());
        }
    }


    /**
     * Deletes an entity of the Table
     *
     * @param DomainEntity $entity the entity
     *
     */
    final public function delete(DomainEntity $entity) : void
    {
        try {
            $this->crudValidation($entity->getID())
                 ->getConnector()
        ->exec("DELETE FROM {$entity::getTable()} WHERE id={$entity->getID()}");
        } catch (NegativIdException $e) {
        } catch (QueryException $e) {
        }
    }

    /**
     * Validates before Executing crud Function
     *
     * @param int $id the id to made an crud action
     *
     * @return static for method chaining
     * @throws NegativIdException if id < 0
     */
    final protected function crudValidation(int $id) : Entity
    {
        if ($id < 0) {
            throw new NegativIdException('cant execute crud function on id < 0');
        }
        return $this;
    }

    /**
     * The PDO query implementation
     *
     * @param string $statement the sql string
     *
     * @return DomainEntity|null query result 
     * @throws QueryException
     */
    final public function querySingle(string $statement) : ?DomainEntity
    {
        $result = $this->getConnector()->querySingle($statement);
        return empty($result) ? null : $this->instanceFromDB($result);
    }

    /**
     * @param string $statement
     *
     * @return DomainEntity[]
     * @throws QueryException
     */
    final protected function queryMultiple(string $statement) : array
    {
        $arr = array();
        foreach ($this->getConnector()->queryMultiple($statement) as $res) {
            $instance = $this->instanceFromDB($res);
            array_push($arr,  $instance);
        }
        return $arr;
    }
}
