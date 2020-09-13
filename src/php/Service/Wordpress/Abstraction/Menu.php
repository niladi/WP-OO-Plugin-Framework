<?php


namespace WPPluginCore\Service\Wordpress\Abstraction;
defined('ABSPATH') || exit;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\View\MainView;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\View\Abstraction\View;

abstract class Menu extends Service
{

    private const SUB_NAME = 'name';
    private const SUB_SLUG = 'slug';
    private const SUB_VIEW_CLASS = 'view_class';

    private array $subMenuEntries;
    private Plugin $plugin;

    /**
     * @var Menu[]
     */
    private static array $menus  = array();

    protected function __construct()
    {
        $this->subMenuEntries = array();
        $this->addSubMenuEntries();
    }

    private function setPlugin(Plugin $plugin)
    {
        if (isset($this->plugin)) {
            throw new IllegalStateException('the plugin is already setted');
        }
        $this->plugin = $plugin;
    }

    private function getPlugin()
    {
        if (!isset($this->plugin)) {
            $this->setPlugin(Plugin::getFirst());
        }
        return $this->plugin;
    }

    /**
     * @inheritDoc
     */
    static public function registerMe(Plugin $plugin): void 
    {
        parent::registerMe($plugin);
        $instance = static::getInstance();
        $instance->setPlugin($plugin);
        array_push(self::$menus, $instance);
        add_action('admin_menu', array(static::class, 'addMainMenus'));
    }

    public static function addMainMenus()
    {
        foreach (self::$menus as $menu) {
            $menuSlug =  $menu->getSlug();
            add_menu_page(
                static::getLabel(),
                static::getLabel(),
                'manage_options',
                $menuSlug,
                array(static::getMainView(), 'show'),
                "",
                20
            );
    
            foreach ($menu->subMenuEntries as $subMenuEntry) {
                add_submenu_page(
                    $menuSlug,
                    $subMenuEntry[self::SUB_NAME],
                    $subMenuEntry[self::SUB_NAME],
                    'manage_options',
                    $menu->getPlugin()->buildKey('menu-' . $subMenuEntry[self::SUB_SLUG]),
                    array($subMenuEntry[self::SUB_VIEW_CLASS], 'show')
                );
            }
        }
    }

    public function getSlug() : string
    {
        return $this->getPlugin()->buildKey('main-menu');
    }

    abstract static function getMainView() : string;

    abstract static function getLabel() : string;

    abstract function addSubMenuEntries(): void;

    public function addSubMenuEntry(string $name, string $slug, string $viewClass)
    {
        if (!is_subclass_of($viewClass, View::class) ) {
            throw new IllegalArgumentException('$viewClass has to be of type ' . View::class . ' is actually: ' . $viewClass);
        }
        array_push($this->subMenuEntries, array(self::SUB_NAME  => $name, self::SUB_SLUG => $slug, self::SUB_VIEW_CLASS => $viewClass));
    }
}
