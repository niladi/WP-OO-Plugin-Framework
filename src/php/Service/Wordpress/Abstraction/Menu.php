<?php


namespace WPPluginCore\Service\Wordpress\Abstraction;
defined('ABSPATH') || exit;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\View\MainView;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\View\Abstraction\View;

abstract class Menu extends Service
{

    private const SUB_NAME = 'name';
    private const SUB_SLUG = 'slug';
    private const SUB_VIEW_CLASS = 'view_class';

    private array $subMenuEntries;

    private static array $menus  = array();

    protected function __construct()
    {
        $this->subMenuEntries = array();
        $this->addSubMenuEntries();
    }

    /**
     * @inheritDoc
     */
    static public function registerMe(Plugin $plugin): void 
    {
        parent::registerMe($plugin);
        self::$menus[$plugin->getSlug()] = static::getInstance();
        add_action('admin_menu', array(static::class, 'addMainMenus'));
    }

    public static function addMainMenu()
    {
        foreach (self::$menus as $slug => $menu) {
            $menuSlug =  Plugin::get($slug)->buildKey('main-menu');
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
                    $subMenuEntry[self::SUB_SLUG],
                    array($subMenuEntry[self::SUB_VIEW_CLASS], 'show')
                );
            }
        }
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
