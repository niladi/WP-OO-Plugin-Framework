<?php


namespace WPPluginCore\Service\Wordpress;
defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use WPPluginCore\Domain\Helper\Menu as MenuHelper;
use WPPluginCore\Service\Abstraction\Service;


class Menu extends Service
{

    private MenuHelper $menu;

    public function __construct(LoggerInterface $logger, MenuHelper $menu)
    {
        parent::__construct($logger);
        $this->menu = $menu;
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        parent::registerMe();
        add_action('admin_menu', array($this->menu, 'addMe'));
    }

    public function getSlug() : string
    {
        return $this->menu->getSlug();
    }
}