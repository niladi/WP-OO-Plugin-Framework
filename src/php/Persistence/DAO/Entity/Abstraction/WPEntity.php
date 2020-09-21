<?php

namespace WPPluginCore\Persistence\DAO\Entity\Abstraction;

defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use WP_Error;
use WPPluginCore\Logger;
use WPPluginCore\Exception\ReadException;
use WPPluginCore\Exception\QueryException;
use WPPluginCore\Exception\WPDAOException;
use WPPluginCore\Exception\UpdateException;
use WPPluginCore\Persistence\EntityFactory;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\NegativIdException;
use WPPluginCore\Persistence\DAO\Adapter\DBConnector;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\Entity as DomainEntity;
use WPPluginCore\Persistence\Domain\Entity\Abstraction\WPEntity as DomainWPEntity;

/**
 * A more specific entity
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
abstract class WPEntity extends Entity
{
    protected Save $save;

    public function __construct(EntityFactory $entityFactory,DBConnector $dBConnector, LoggerInterface $logger, Save $save)
    {
        parent::__construct($entityFactory, $dBConnector, $logger);
        $this->save = $save;
    }

    /**
     * @param int $wpPostID
     *
     * @return DomainWPEntity null if nothing is found
     * @throws \WPPluginCore\Exception\AttributeException
     */
    public function readFromWPPostID(int $wpPostID) : ?DomainWPEntity
    {
        try {
            return $this->crudValidation($wpPostID)
                            ->readSingleByKeyValue(
                                DomainWPEntity::KEY_WP_POST_ID,
                                $wpPostID
                            );
        } catch (NegativIdException | QueryException $ex) {
            $this->logger->error('exception od query: '. $ex->getTraceAsString());
            exit;
        }
    }

    /**
     * Create an new Entry of PostType
     *
     * @param DomainEntity $entity
     *
     * @return bool
     * @throws WPDAOException if something went wrong
     */
    public function create(DomainEntity $entity) : bool
    {
        if ($entity instanceof DomainWPEntity) {
            $keyPostId = DomainWPEntity::KEY_WP_POST_ID;

            if ($entity->getAttributeValue($keyPostId) <= 0) {
                $wpPostID = wp_insert_post(array(
                                                 'post_title'  => $entity->getWPPostTitle(),
                                                 'post_type'   => $entity::getSlug(),
                                                 'post_status' => 'Private'
                                             )) ;
    
                if ($wpPostID == 0 || $wpPostID instanceof WP_Error) {
                    throw new WPDAOException('Cant Create Wordpress Post');
                }
    
                $entity->setAttributeValue($keyPostId, $wpPostID);
            }
       
            if (!parent::create($entity)) {
                wp_delete_post($entity->getAttributeValue($keyPostId));
                return false;
            }
            $this->save->updateTitle($entity);
            return true;
        }
        return parent::create($entity);
    }

    /**
     * @param DomainWPEntity $entity
     *
     * @throws UpdateException
     */
    public function update(DomainEntity $entity): void
    {
        parent::update($entity);
        if ($entity instanceof DomainWPEntity ) {
            $this->save->updateTitle($entity);
        }
    }
}
