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
    private MetaboxWrapper $wrapper;
    private MetaboxView $view;

    public function __construct(LoggerInterface $logger, MetaboxWrapper $wrapper, MetaboxView $view)
    {
        $this->logger = $logger;
        $this->wrapper = $wrapper;
        $this->view = $view;
    }

    public function create(WPEntityDAO $dao)
    {
        return new Metabox($this->logger, $dao, $this->wrapper, $this->view);
    }
}
