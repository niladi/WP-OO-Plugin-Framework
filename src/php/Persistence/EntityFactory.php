<?php


namespace WPPluginCore\Persistence;

use WPPluginCore\Logger;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity as EntityDAO;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\WPEntity as AbstractionWPEntity;


defined('ABSPATH') || exit;

/**
 * The Factory for the entities and the mapping of them
 * 
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class EntityFactory implements IBaseFactory
{
    /**
     * @var array 
     */
    private array $entitiesMap;

    private static ?self $instance = null;

    private function __construct()
    {
        $this->entitiesMap = array();
    }

    /**
     * @inheritDoc
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns a new Entity
     * 
     * @param EntityDAO $dao the dao instance
     * @param array $metaarr the typical metarray
     * @return Entity a new Entity
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function newEntity(EntityDAO $dao,array $metaarr = array()) : Entity
    {
        return $this->getEntityByDAO($dao)::init($metaarr);
    }


    /**
     * helper function to add an entity to the entitiesMap array, where the key is the post type
     *
     * @param string $domain the class of the domain entity
     * @param EntityDAO $dao the class of the dao entity
     * @return self for better methot chainng
     *
     * @throws IllegalArgumentException if $dao or $domain are not subclasses of Domain/Entity or DAO/Entity
     */
    public function addEntity(string $domain, EntityDAO $dao): self
    {
        if (!is_subclass_of($domain, Entity::class)) {
            throw new IllegalArgumentException("the domain class is not subclass of Domain/Entity");
        }
        $this->entitiesMap[$domain] = $dao;
        return $this;
    }


    /** 
     * Helper function to get the  Wordpress Entity dao instance of the slug
     *
     * @param string $slug the key in entitiesMap
     *
     * @return WPEntity the instance of the dao of the slug
     * @throws IllegalArgumentException if the slug is not an registered key 
     */
    public function getWPEntityDAOInstanceBySlug(string $slug): WPEntity
    {
        return $this->entitiesMap[$this->getWPEntityBySlug($slug)];
    }

    /** 
     * Helper function to get the  Wordpress Entity dao instance of the domain
     *
     * @param string $class the key in entitiesMap
     *
     * @return WPEntity the instance of the dao of the slug
     * @throws IllegalArgumentException if the slug is not an registered key 
     */
    public function getWPEntityDAOInstance(string $class): WPEntity
    {
        if (in_array($class, $this->getWPEntites())) {
            return $this->entitiesMap[$class];
        }
        throw new IllegalArgumentException('The class is not added');
    }



    /** 
     * Helper function to get the  Wordpress Entity dao instance of the domain
     *
     * @param string $class the key in entitiesMap
     *
     * @return WPEntity the instance of the dao of the domain
     * @throws IllegalArgumentException if the slug is not an registered key 
     */
    public function getEntityDAOInstance(string $class): WPEntity
    {
        if (array_key_exists($class, $this->entitiesMap)) {
            return $this->entitiesMap[$class];
        }
        throw new IllegalArgumentException('The class is not added');
    }

    /**
     * Retunr the wordpress entity by his slug
     *
     * @param string $slug the slug of the entity
     * @return string the class name of the entity
     * @throws IllegalArgumentException if there is no entity fo the slug
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getWPEntityBySlug(string $slug)
    {
        foreach ($this->getWPEntites() as $class) {
            if ($class::getSlug() === $slug) {
                return $class;
            }
        }
        throw new IllegalArgumentException('Slug: ' . $slug . ' does not exists');
    }


    /**
     * Retunrn the entity classes
     *
     * @return string[] the entity class names
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getEntities(): array
    {
        return array_keys($this->entitiesMap);
    }

    /**
     * Returns al the wordpress entity classes
     *
     * @return string[] the WPEntity class names
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getWPEntites(): array
    {
        return array_filter($this->getEntities(), fn ($class) => is_subclass_of($class, AbstractionWPEntity::class));
    }

    /**
     * Retunrns the entity class by its dao
     *
     * @param EntityDAO $dao
     * @return string the Entity by the $dao
     * @author Niklas Lakner niklas.lakner@gmail.com
     * @throws IllegalArgumentException if there is no entity for this dao
     */
    public function getEntityByDAO(EntityDAO $dao)
    {
        foreach ($this->entitiesMap as $key => $value) {
            if ($dao == $value) {
                return $key;
            }
        }
        throw new IllegalArgumentException('There is no entity for this dao');
    }

    /**
     * Returns the dao of the class
     *
     * @param string $class the class of the dao
     * @return EntityDAO the instance of the dao
     * @author Niklas Lakner niklas.lakner@gmail.com
     * @throws IllegalArgumentException if the number of daos of this class is not null
     */
    public function getDAOByClass(string $class)
    {
        $return = $this->getDAOsByClass($class);
        if (sizeof($return) === 1) {
           return $return[array_key_first($return)];
        } 
        throw new IllegalArgumentException("There are multiple or none instances of this dao"); 
    }



    /**
     * Returns all the daos which are of the type of class
     *
     * @param string $class the class of the dao
     * @return EntityDAO[] an array of DAO entities
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private function getDAOsByClass(string $class)
    {
        return array_filter(array_values($this->entitiesMap), fn($dao) => $dao instanceof $class);
    }
    
}
