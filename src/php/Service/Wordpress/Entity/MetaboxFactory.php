<?php


namespace WPPluginCore\Service\Wordpress\Entity;

use WP_Post;
use WPPluginCore\Logger;
use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\View\Implementation\MetaboxWrapper;
use WPPluginCore\View\Implementation\Metabox as MetaboxView;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity as WPEntityDAO;
use WPPluginCore\Persistence\DAO\Entity\Container\WPEntityContainer;

class MetaboxFactory
{

    private LoggerInterface $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create(WPEntityDAO $dao,  MetaboxWrapper $metaboxWrapper, MetaboxView $metaboxView)
    {
        return new Metabox($this->logger, $dao, $metaboxWrapper, $metaboxView);
    }
}
