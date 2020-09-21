<?php

namespace WPPluginCore\Persistence\DAO\Entity\Container;

use Psr\Container\ContainerInterface;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;

defined('ABSPATH') || exit;


/**
 * An entity container
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class WPEntityContainer implements ContainerInterface
{
    /**
     * @var WPEntity[]
     */
    private array $wpEnities = array();

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
    public function set(string $slug, WPEntity $wpEntity) 
    {
        $this->wpEnities[$slug] = $wpEntity;
    }

    /**
     * Returns all the wpEntites
     *
     * @return WPEntity[]
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getAll() : array
    {
        return array_values($this->wpEnities);
    }
}