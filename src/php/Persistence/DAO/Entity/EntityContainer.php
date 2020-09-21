<?php

namespace WPPluginCore\Persistence\DAO\Entity;

use Psr\Container\ContainerInterface;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;

defined('ABSPATH') || exit;


/**
 * An entity container
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class EntityContainer implements ContainerInterface
{
    /**
     * @var entity
     */
    private array $enities = array();

    /**
     * Returns the WPEnity by its slug
     *
     * @param string $id the slug of the Entity
     * @return WPEntity
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->wpEnities[$id];
        }
        throw new IllegalKeyException();
    }

    /**
     * Checks if the slug exists in the container
     *
     * @param [type] $id
     * @return boolean
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function has($id)
    {
        return array_key_exists($id, $this->wpEnities);
    }

    /**
     * Sets an entity into the dao contaienr
     *
     * @param string $slug
     * @param WPEntity $wpEntity
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function set(string $tableName, Entity $entity) 
    {
        $this->wpEnities[$tableName] = $entity;
    }

    /**
     * Returns an array of an entity
     *
     * @return Entity[]
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getAll() : array
    {
        return array_values($this->enities);
    }
}