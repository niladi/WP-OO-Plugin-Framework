<?php


namespace WPPluginCore\Service\Abstraction;
defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use WPPluginCore\Abstraction\Registerable;

abstract class Service extends Registerable {

    protected LoggerInterface $logger;


    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

}
