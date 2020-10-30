<?php



namespace WPPluginCore\Domain\Helper;

use Psr\Log\LoggerInterface;
use WPPluginCore\Util\SlugBuilder;
use WPPluginCore\Domain\Helper\Menu;
use WPPluginCore\View\Abstraction\View;
use WPPluginCore\Service\Abstraction\Service;

defined('ABSPATH') || exit;

class MenuBuilder 
{
    private string $slug;
    private string $pluginSlug;
    private string $label;
    private View $view;

    private array $subMenuEntries = array();

    public function __construct(string $pluginSlug, string $label, View $view )
    {
        $this->pluginSlug = $pluginSlug;
        $this->slug = $this->buildSlug('main');
        $this->label = $label;
        $this->view = $view;
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

    public function build() : Menu
    {
        return new Menu($this->slug, $this->label, $this->view, Menu::TYPE_MAIN_MENU, '', $this->subMenuEntries);
    }
}