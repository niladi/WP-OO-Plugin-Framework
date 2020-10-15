<?php


namespace WPPluginCore\Service\Wordpress;

use Psr\Log\LoggerInterface;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Util\SlugBuilder;
use WPPluginCore\View\Abstraction\View;

defined('ABSPATH') || exit;

class MenuBuilder 
{
    private string $slug;
    private string $pluginSlug;
    private string $label;
    private View $view;

    private array $subMenuEntries = array();

    public function __construct(string $pluginSlug )
    {
        $this->pluginSlug = $pluginSlug;
        $this->slug = $this->buildSlug('main');
    }

    public function addSubMenuEntry(string $label, string $key, View $view) : self
    {
        array_push($this->subMenuEntries, new Menu( $this->buildSlug($key),$label, $view, Menu::TYPE_SUB_MENU, $this->slug));
        return $this;
    }

    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }
    public function setView(View $view) : self
    {
        $this->view = $view;
        return $this;
    }
    public function buildSlug(string $key) : string
    {
        return SlugBuilder::buildSlug($this->pluginSlug, $key, 'menu' );
    }

    public function build(LoggerInterface $logger) : Menu
    {
        return new Menu($this->slug, $this->label, $this->view, Menu::TYPE_MAIN_MENU, '', $this->subMenuEntries, $logger);
    }
}