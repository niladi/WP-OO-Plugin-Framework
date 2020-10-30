<?php

namespace WPPluginCore\Web\Implementation;

use JsonException;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WPPluginCore\Web\Abstraction\APIAdmin;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Domain\Entity\Abstraction\Entity as AbstractionEntity;
use WPPluginCore\Exception\IllegalArgumentException;

defined('ABSPATH') || exit;

class EntityREST extends APIAdmin 
{

    protected Entity $entityDAO;

    /**
     * @var class-string<AbstractionEntity> $entityClass
     */
    protected string $entityClass;

    /**
     * @psalm-param class-string<AbstractionEntity> $entityClass
     */
    public function __construct(string $namespace, Entity $entityDAO, string $entityClass)
    {
        parent::__construct($namespace);

        $this->entityDAO = $entityDAO;
        $this->entityClass = $entityClass;
    }

    protected function addEndpoints(): void 
    {
        $this->addEndpoint('', array($this, 'get'));
        $this->addEndpoint('create', array($this, 'create'), WP_REST_Server::CREATABLE);
        $this->addEndpoint('', array($this, 'update'), WP_REST_Server::EDITABLE);
        $this->addEndpoint('', array($this, 'delete'), WP_REST_Server::DELETABLE);
    }

    protected function parseQueryParams(WP_REST_Request $request) : array
    {
        $params = $request->get_query_params();
        $entity = $this->entityClass::init();
        $return = array();
        foreach ($params as $key => $value) {
            if ($entity->hasAttribute($key)) {
                $return[$key] = $value;
            }
        }
        return $return;
    }


    /**
     * 
     * @param WP_REST_Request $request 
     * @return AbstractionEntity[] 
     */
    private function readByRequest(WP_REST_Request $request) : array
    {
        return $this->entityDAO->readMultipleByArray($this->parseQueryParams($request));
    }

    /**
     * 
     * @param WP_REST_Request $request 
     * @return array|object
     * 
     * @throws JsonException 
     */
    private function parseBody(WP_REST_Request $request, bool $assoc = false)
    {
        return json_decode($request->get_body(), $assoc, JSON_THROW_ON_ERROR);
    }

    public function get(WP_REST_Request $request) : WP_REST_Response 
    {
        return new WP_REST_Response($this->readByRequest($request));
    }

    public function create(WP_REST_Request $request): WP_REST_Response 
    {
        try {
            $body = $this->parseBody($request, false);
        } catch (JsonException $jsonException) {
            return new WP_REST_Response('Body is not a valid json format', 400);
        }

        if (is_array($body)) {
            foreach($body as $value) {
                $this->entityDAO->createByArray($value);
            }
        } else {
            $this->entityDAO->createByArray((array) $body);
        }


        return new WP_REST_Response('done');
    }

    public function update(WP_REST_Request $request): WP_REST_Response 
    {
        $entities = $this->readByRequest($request);
        try {
            $body = $this->parseBody($request, true);
        } catch (JsonException $jsonException) {
            return new WP_REST_Response('Body is not a valid json format', 400);
        }

        foreach($entities as $entity) {
            foreach($body as $key => $value) {
                try {
                    $entity->setAttributeValue($key, $value);
                } catch (IllegalArgumentException $ex) {
                    return new WP_REST_Response("Value pair can't set. \n key: $key \n value: $value" );
                }
            }
        }

        foreach($entities as $entity) {
            $this->entityDAO->update($entity);
        }

        return new WP_REST_Response('Done', 200);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response 
    {
        $entities = $this->readByRequest($request);
        foreach ($entities as $entity) {
            $this->entityDAO->delete($entity);
        }

        return new WP_REST_Response('done');
    }

}