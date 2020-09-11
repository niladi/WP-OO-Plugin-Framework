<?php

namespace WPPluginCore\Persistence\DAO\Entity\Abstraction;

defined('ABSPATH') || exit;

use FactoryClass;
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
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity as DomainEntity;


/**
 * 
 * @author
 */
abstract class Entity {


    /**
     * Instanciate from DB (should only executed from database, because no errors are thrown)
     *
     * @param array $metaarr 
     */
    final private function instanceFromDB(array $metaarr)  : DomainEntity
    {
        try {
            return (EntityFactory::getInstance()->newEntity($this, $metaarr));
        } catch (IllegalArgumentException $e) {
            Logger::error('Database entry is corrupted: ' . $e->getMessage(), $metaarr);
        }
    }

    /**
     * Gets the the dow of himself. Should only used by concred DAO implementations otherwise the programm will be exitet.
     *
     * @return static
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    final static function getInstance()
    {
        try {
            return EntityFactory::getInstance()->getDAOByClass(static::class);
        } catch (IllegalArgumentException $exception) {
            Logger::error('Tried to get instance of non spezicific DAO');
            exit;
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
            $this->getConnector()->exec(
                sprintf(
                    "INSERT INTO %s (%s) VALUES (%s)",
                    $entity::getTable(),
                    implode(', ', array_keys($arr)),
                    implode(', ', array_values($arr))
                )
            );
        } catch (QueryException $exception) {
            Logger::error('Can\'t create an entity: Error Message: ' . $exception->getMessage(), $arr);
            return false;
        }

        try {
            $id = Parser::strToInt($this->getConnector()->getConnection()->lastInsertId());
            $entity->setID($id);
        } catch (IllegalValueException|ParserException $e) {
            Logger::error('The Value of the int is corrupted ' . $id, $e->getTrace());
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
        return $this->create(EntityFactory::getInstance()->newEntity($this, $metaarr));
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
        return $this->querySingle(sprintf(
            "SELECT * FROM %s WHERE %s",
            $entity::getTable(),
            $entity->attributesForDB($keys, '=', ' AND ')
        ));
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
        return $this->readSingleByEntityKeys(EntityFactory::getInstance()->newEntity($this, $arr), array_keys($arr));
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
        return $this->queryMultiple(sprintf(
            "SELECT * FROM %s WHERE %s",
            $entity::getTable(),
            $entity->attributesForDB($keys, '=', ' AND ')
        ));
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
        return $this->readMultipleByEntityKeys(EntityFactory::getInstance()->newEntity($this, $arr), array_keys($arr));
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
            $this->getConnector()->exec(sprintf("UPDATE %s SET %s WHERE id=%d", $entity::getTable(), $query, $entity->getID()), false);
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
                 ->exec(sprintf("DELETE FROM %s WHERE id=%s", $entity::getTable(), $entity->getID()));
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
     * Returns the instance of an DB connector
     *
     * @return DBConnector the DB connector
     */
    final protected function getConnector() : DBConnector
    {
        return DBConnector::getInstance();
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
