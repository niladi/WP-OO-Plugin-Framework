<?php

namespace WPPluginCore\Persistence\DAO\Entity\Abstraction;

defined('ABSPATH') || exit;

use FactoryClass;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use WPPluginCore\Util\Parser;
use WPPluginCore\Logger;
use WPPluginCore\Domain;
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
use WPPluginCore\Persistence\DB\DBConnector;
use WPPluginCore\Persistence\DAO\Entity\Container\EntityContainer;
use WPPluginCore\Domain\Entity\Abstraction\Entity as DomainEntity;
use WPPluginCore\Domain\Entity\Abstraction\EntityValidator;
use WPPluginCore\Exception\IllegalStateException;

/**
 * @template T of DomainEntity
 * 
 * @author
 */
abstract class Entity 
{

    /**
     * @var class-string<T>
     */
    protected string $entityClass;
    protected DBConnector $dbConnector;
    protected LoggerInterface $logger;

    /**
     * 
     * @param class-string<T> $entityClass 
     * @param DBConnector $dbConnector 
     * @param LoggerInterface $logger 
     * @return void 
     * @throws IllegalStateException 
     * @author Niklas Lakner <niklas.lakner@gmail.com>
     */
    public function __construct(string $entityClass,  DBConnector $dbConnector, LoggerInterface $logger)
    {
        if (!is_subclass_of($entityClass, DomainEntity::class)) {
            throw new IllegalStateException('Illgela entiyt class');
        }
        $this->entityClass = $entityClass;
        $this->dbConnector = $dbConnector;
        $this->logger = $logger;
    }
    /**
     * Instanciate from DB (should only executed from database, because no errors are thrown)
     *
     * @param array $metaarr 
     * 
     * @return T
     */
    final private function instanceFromDB(array $metaarr)  
    {
        try {
            return ( $this->entityClass::init($metaarr));
        } catch (IllegalArgumentException $e) {
            throw new IllegalStateException('Database entry is corrupted: ' . \var_export($metaarr, \true), 0, $e);
        }
    }

    /**
     * Create a new Entry of an Enityt
     *
     * @param DomainEntity $entity
     *
     * @return bool true if everything is
     * @throws IllegalStateException if the entity or the database is corrupted
     */
    public function create(DomainEntity $entity) : bool
    {
        $arr = $entity->getAttributesValuesAssoc();
        unset($arr[DomainEntity::KEY_ID]);
        $keys = implode(', ', array_keys($arr));
        $values = implode(', ', array_values($arr));
        $table = $entity::getTable();
        try {
            $this->dbConnector->exec(
                "INSERT INTO $table ($keys) VALUES ($values)"
            );
        } catch (QueryException $exception) {
            throw new IllegalStateException('Can\'t create an entity', 0, $exception);
        }

        try {
            $id = Parser::strToInt($this->dbConnector->getConnection()->lastInsertId());
            $entity->setID($id);
        } catch (IllegalValueException|ParserException $e) {
            throw new IllegalStateException('The Value of the int is corrupted ', 0, $e);
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
        return $this->create($this->entityClass::init( $metaarr));
    }
    /**
     *
     * Read an entity of the Database by its id
     *
     * @param int $id the entity which should deleted
     *
     * @return ?T
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
     * @return ?T
     */
    public function readSingleByEntityKeys(DomainEntity $entity, array $keys) : ?DomainEntity
    {
        return $this->querySingle("SELECT * FROM {$entity::getTable()} WHERE {$entity->attributesForDB($keys, '=', ' AND ')}");
    }

    /**
     * @param array $arr
     * @param bool $single
     *
     * @return ?T
     * @throws AttributeException
     * @throws QueryException
     */
    public function readSingleByArray(array $arr) : ?DomainEntity
    {
        return $this->readSingleByEntityKeys($this->entityClass::init($arr), array_keys($arr));
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return ?T
     * @throws QueryException
     */
    public function readSingleByKeyValue(string $key, int $value) : ?DomainEntity
    {
        return $this->readSingleByArray(array($key => $value));
    }


    /**
     * Returns the entities filtered by the values from the key at the enitty
     *
     * @param DomainEntity $entity the enity from which the values should be getted
     * @param array $keys the keys that specifies the filter
     *
     * @return T[] the whole entities or null if nothing is found
     * @throws IllegalKeyException if the is one unvalid key
     * @throws QueryException if an error occurs on the query
     */
    public function readMultipleByEntityKeys(DomainEntity $entity, array $keys) : array
    {
        $where = isset($keys) && !empty($keys) ? 'WHERE ' . $entity->attributesForDB($keys, '=', ' AND ') :'';
        $table = $entity::getTable();
        return $this->queryMultiple(
            "SELECT * FROM $table $where");
    }

    /**
     * Returns the Entities
     *
     * @param array $arr if empty all Entities are returned
     *
     * @return DomainEntity[]
     * @throws QueryException
     */
    public function readMultipleByArray(array $arr = array()) : array
    {
        return $this->readMultipleByEntityKeys($this->entityClass::init($arr), array_keys($arr));
    }
    /**
     * @template TValue
     * 
     * @param string $key
     * @param TValue $value
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
        try {
            $this->dbConnector->exec("UPDATE {$entity::getTable()} SET {$query} WHERE id={$entity->getID()}", false);
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
                 ->dbConnector->exec("DELETE FROM {$entity::getTable()} WHERE id={$entity->getID()}");
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
     * @return ?T query result 
     * @throws QueryException
     */
    final public function querySingle(string $statement) : ?DomainEntity
    {
        $result = $this->dbConnector->querySingle($statement);
        return empty($result) ? null : $this->instanceFromDB($result);
    }

    /**
     * @param string $statement
     *
     *
     * @throws QueryException
     *
     * @return T[]
     */
    final protected function queryMultiple(string $statement) : array
    {
        $arr = array();
        foreach ($this->dbConnector->queryMultiple($statement) as $res) {
            $instance = $this->instanceFromDB($res);
            array_push($arr,  $instance);
        }
        return $arr;
    }
}
