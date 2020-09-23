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

    private MetaboxWrapper $metaboxWrapper;
    private MetaboxView $metaboxView;
    private LoggerInterface $logger;


    public function __construct(LoggerInterface $logger, MetaboxWrapper $metaboxWrapper, MetaboxView $metaboxView)
    {
        $this->logger = $logger;
        $this->metaboxWrapper = $metaboxWrapper;
        $this->metaboxView = $metaboxView;
    }

    public function create(WPEntityDAO $dao)
    {
        return new Metabox($this->logger, $dao, $this->metaboxWrapper, $this->metaboxView);
    }
}
