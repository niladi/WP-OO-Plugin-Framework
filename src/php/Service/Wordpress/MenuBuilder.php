<?php


namespace WPPluginCore\Service\Wordpress;

use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Util\SlugBuilder;

defined('ABSPATH') || exit;

class MenuBuilder 
{
    private string $slug;
    private string $pluginSlug;
    private string $label;
    private string $viewClass;

    private array $subMenuEntries = array();

    public function __construct(string $pluginSlug )
    {
        $this->pluginSlug = $pluginSlug;
        $this->slug = $this->buildSlug('main');
    }

    public function addSubMenuEntry(string $label, string $key, string $viewClass) : self
    {
        array_push($this->subMenuEntries, new Menu( $this->buildSlug($key),$label, $viewClass, Menu::TYPE_SUB_MENU, $this->slug));
        return $this;
    }

    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }
    public function setViewClass(string $viewClass) : self
    {
        $this->viewClass = $viewClass;
        return $this;
    }
    public function buildSlug(string $key) : string
    {
        return SlugBuilder::buildSlug($this->pluginSlug, $key, 'menu' );
    }

    public function build(LoggerInterface $logger) : Menu
    {
        return new Menu($this->slug, $this->label, $this->viewClass, Menu::TYPE_MAIN_MENU, '', $this->subMenuEntries, $logger);
    }
}