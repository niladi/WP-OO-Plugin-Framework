<?php


namespace WPPluginCore\Service\Abstraction;
defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Abstraction\Registerable;
use WPPluginCore\Abstraction\IRegisterable;
use WPPluginCore\Abstraction\RegisterableFactory;

abstract class Service extends Registerable {

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}
