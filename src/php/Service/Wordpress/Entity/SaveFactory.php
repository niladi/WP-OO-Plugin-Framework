<?php


namespace WPPluginCore\Service\Wordpress\Entity;


use Psr\Log\LoggerInterface;

use WPPluginCore\Service\Abstraction\Service;

use WPPluginCore\Domain\Entity\Abstraction\EntityValidator;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\WPEntity;

class Save 
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create(WPEntity $wPEntity, ?EntityValidator $entityValidator) : Save
    {
        return new Save($this->logger, $wPEntity, $entityValidator);
    }
}
