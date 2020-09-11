<?php


namespace WPPluginCore\Service\Wordpress\Abstraction;
defined('ABSPATH') || exit;
use WPPluginCore\App;
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

    protected function __construct()
    {
        $this->subMenuEntries = array();
        $this->addSubMenuEntries();
    }

    /**
     * @inheritDoc
     */
    public static function registerMe() : void
    {
        parent::registerMe();
        add_action('admin_menu', array(static::getInstance(), 'addMainMenu'));
    }

    public function addMainMenu()
    {
        add_menu_page(
            static::getLabel(),
            static::getLabel(),
            'manage_options',
            static::getSlug(),
            array(static::getMainView(), 'show'),
            "",
            20
        );

        foreach ($this->subMenuEntries as $subMenuEntry) {
            add_submenu_page(
                static::getSlug(),
                $subMenuEntry[self::SUB_NAME],
                $subMenuEntry[self::SUB_NAME],
                'manage_options',
                $subMenuEntry[self::SUB_SLUG],
                array($subMenuEntry[self::SUB_VIEW_CLASS], 'show')
            );
        }
    }

    public static function getSlug() : string 
    {
        return App::getSlug() . '-' . 'main-menu';
    }

    abstract static function getMainView() : string;

    abstract static function getLabel() : string;

    abstract function addSubMenuEntries(): void;

    public function addSubMenuEntry(string $name, string $slug, string $viewClass)
    {
        if (!is_suclass_of($viewClass, View::class) ) {
            throw new IllegalArgumentException('$viewClass has to be of type ' . View::class . ' is actually: ' . $viewClass);
        }
        array_push($this->subMenuEntries, array(self::SUB_NAME  => $name, self::SUB_SLUG => $slug, self::SUB_VIEW_CLASS => $viewClass));
    }
}
