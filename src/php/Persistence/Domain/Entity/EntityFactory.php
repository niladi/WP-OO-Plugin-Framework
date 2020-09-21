<?php




namespace WPPluginCore\Persistence;

use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity;

defined('ABSPATH') || exit;

/**
 * The Factory for the entities and the mapping of them
 * 
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class EntityFactory
{
    private string $entityClass;

    public function __construct(string $entityClass)
    {
        if (!is_subclass_of($entityClass, Entity::class)) {
            throw new IllegalArgumentException("the given class ". $entityClass . " is not of type Entity " . Entity::class);
        }
        $this->entityClass = $entityClass;
    }

    public function entity($metarr = array()) : Entity
    {
        return $this->entityClass::init($metarr);
    }

    public function getEntityClass() : string
    {
        return $this->entityClass;
    }
}
