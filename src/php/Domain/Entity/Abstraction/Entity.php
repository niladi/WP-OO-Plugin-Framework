<?php

namespace WPPluginCore\Domain\Entity\Abstraction;

defined('ABSPATH') || exit;

use Error;
use WPPluginCore\Exception\DuplicateException;
use WPPluginCore\Exception\DuplicateKeyException;
use WPPluginCore\Exception\EntityException;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Exception\IllegalValueException;
use WPPluginCore\Persistence\DAO;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Domain;
use WPPluginCore\Domain\Entity\Attribute\Implementation\EntityID;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Logger;

/**
 * Undocumented class
 * 
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
abstract class Entity
{

    /*
     * **************************************************************
     * Variables SECTION
     * **************************************************************
     *
     */

    /**
     * Key for notes attribute
     */
    public const KEY_ID = 'id';

    /**
     * @var Attribute[] list of Metavalues
     */
    protected array $attributes = array();

    /*
     * **************************************************************
     * Abstract SECTION
     * **************************************************************
     *
     */



    /**
     * Returns the Datatype of the ID
     *
     * @return string type as String
     */
    abstract protected static function getIDIntType() : string;

    /**
     * Returns the Database Table Name
     *
     * @return string database table name
     */
    abstract public static function getTable(): string;

    /*
     * **************************************************************
     * Magic Method SECTION
     * **************************************************************
     *
     */
    public function validate() : Entity
    {
        //Todo boolean could be better
        return $this;
    }
    /**
     * add all meta data
     *
     * @throws DuplicateKeyException if there are duplicate KEYs of one mtea value
     */
    public function addAttributes(): void
    {
        $this->addAttribute(new EntityID(self::KEY_ID, 'ID', static::getIDIntType()));
    }

    /**
     * LS_A_Post_Type constructor. The metaName of the PostType is the Slug
     *
     * @param array $attributes attributes
     *
     * @throws IllegalKeyException if attributes include key which is not set
     * @throws IllegalValueException the value is not valid
     */
    public function __construct(array $attributes = array())
    {
        try {
            $this->addAttributes();
        } catch (DuplicateKeyException $exception) {
            throw new IllegalStateException('Tried to instanciate attribute with duplicate keys, should be an illegal state and never occurs', $exception->getTrace());
        }
        foreach ($attributes as $key => $value) {
            $this->setAttributeValue($key, $value);
        }
    }

    /**
     * Initlializes a new entity
     *
     * @param array $attributes the default meta array
     * @return static the new entity
     * 
     * @throws IllegalKeyException if attributes include key which is not set
     * @throws IllegalValueException the value is not valid
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public static function init(array $attributes = array())
    {
        return new static($attributes);
    } 

    /**
     * Getter Function for all the data in a post type
     *
     * @param $property string the key you want to have
     *
     * @return Attribute|mixed|null
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } elseif (key_exists($property, $this->attributes)) {
            return $this->attributes[ $property ];
        }
        return null;
    }

    /**
     * Executes __get
     *
     * @param $property string for Key
     *
     * @return mixed|Attribute|null
     */
    public function get(string $property)
    {
        return $this->__get($property);
    }

    /**
     * Returns the id of the post
     *
     * @return int the id of the post
     */
    final public function getID() : int
    {
        try {
            return $this->getAttributeValue(self::KEY_ID);
        } catch (IllegalKeyException $e) {
            throw new IllegalStateException('There is no way an illegal key exception is thrown');
        }
    }

    /**
     * Setts the ID
     *
     * @param int $id the new id
     *
     * @throws IllegalValueException if the ID is not valid
     */
    final public function setID(int $id = -1) : void
    {
        try {
            $this->setAttributeValue(self::KEY_ID, $id);
        } catch (IllegalKeyException $e) {
            throw new IllegalStateException('There is no way an IllegalKeyException is throwed');
        }
    }

    /**
     * Return the
     *
     * @return Attribute[]
     */
    final public function getAttributes() : array
    {
        return $this->attributes;
    }

    final public function hasAttribute(string $key) : bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Returns an array of Strings within the attributes
     *
     * @param bool $withoutID default true exluding the key for id
     *
     * @return string[] the attribute keys
     */
    final public function getAttributesKeys(bool $withoutID = true) : array
    {
        return ! $withoutID ? array_keys($this->attributes) : $this->getAttributesKeysWithoutID();
    }

    final private function getAttributesKeysWithoutID() : array
    {
        $arr = array_keys($this->attributes);
        $index = array_search(self::KEY_ID, $arr);
        unset($arr[$index]);
        return $arr;
    }

    /*
     * **************************************************************
     * Meta Information SECTION
     * **************************************************************
     *
     */

    /**
     * Get the Attribute at the key
     *
     * @param string $key key of attribute
     *
     * @return Attribute the Meta Attribute
     * @throws IllegalKeyException if the Key ist not found
     */
    final public function getAttribute(string $key) : Attribute
    {
        if (key_exists($key, $this->attributes)) {
            return $this->attributes [$key];
        }
        throw new IllegalKeyException();
    }

    /**
     * @param string $key
     *
     * @return string
     * @throws IllegalKeyException
     */
    final public function getAttributeValueForDB(string $key) : string
    {
        return $this->getAttribute($key)->getValueForDB();
    }

    /**
     * add a new attribute
     *
     * @param Attribute $attribute the attribute which should get added
     * @throws DuplicateKeyException if try to add key that already exists
     */
    final protected function addAttribute(Attribute $attribute) : void
    {
        $key = $attribute->getKey();
        if (array_key_exists($key, $this->attributes)) {
            throw new DuplicateKeyException();
        }
        $this->attributes[ $key ] = $attribute;
    }

    /**
     * Returns the meta value of meta attribute at key
     *
     * @param string $key of the meta attribute
     *
     * @return mixed the value of the meta attribute
     *
     * @throws IllegalKeyException
     */
    final public function getAttributeValue(string $key)
    {
        return $this->getAttribute($key)->getValue();
    }



    /**
     * Sets the Attribute
     *
     * @param string $key of the meta attribute
     * @param $value mixed to set
     *
     * @return $this returns this for method chaining
     * @throws IllegalKeyException if the key not found
     * @throws IllegalValueException if the value is not valid
     */
    final public function setAttributeValue(string $key, $value)
    {
        $this->getAttribute($key)->setValue($value);
        return $this;
    }

    /**
     * Returns the Attributes Values as Associative arrays
     *
     * @return array returns the new array
     */
    final public function getAttributesValuesAssoc(bool $valueForDB = true)
    {
        $arr = array();
        foreach ($this->attributes as $key => $value) {
            if ($valueForDB) {
                $arr[$key]  = $value->getValueForDB();
            } else {
                $arr[$key]  = $value->getValue();
            }
        }
        return $arr;
    }

    /**
     * Serializes specific attributes for an query string
     *
     * @param array $keys the keys auf the attributes which should serialized
     * @param string $relational_operator how key und value are connected
     * @param string $connector how each key,value pair is connected
     *
     * @return string the serialized string
     * @throws IllegalKeyException if a key is used which is not defined in Attributes
     */
    final public function attributesForDB(array $keys, string $relational_operator='=', string $connector=', ') : string
    {
        $s = '';
        foreach ($keys as $key) {
            $s .= $key . $relational_operator . $this->getAttribute($key)->getValueForDB() . $connector;
        }
        return rtrim($s, $connector);
    }

    /**
     * validates the data semantic before save
     */
    public function beforeSave() : void
    {
        //Place holder
    }

    /**
     * Do somthig the data semantic after save
     */
    public function afterSave() : void
    {
        //Place holder
    }

    /*
     * **************************************************************
     * Table Creation SECTION
     * **************************************************************
     *
     */

    /**
     * Returns the Primary Key's
     *
     * @return string[]
     */
    protected static function getPrimaryKeys() : array
    {
        return array( self::KEY_ID );
    }

    final public function getPrimaryKeysSerialized() : string
    {
        return self::connectKeys(self::getPrimaryKeys());
    }

    /**
     * Returns an assoc array as $key: self::connectKeys($keys)
     *
     * @return string[]
     */
    public function getForeignKeys() : array
    {
        return array(
            // todo
        );
    }

    final protected static function getKeysAsForeignKeys(array $keys) : string
    {
        return sprintf('%s (%s)', static::getTable(), self::connectKeys($keys));
    }

    final public static function connectKeys(array $keys, string $glue = ', ') : string
    {
        return implode($glue, $keys);
    }

    final public function placeholder()
    {
    }
}
