<?php


namespace WPPluginCore\Service\Wordpress;
defined('ABSPATH') || exit;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\View\MainView;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Util\SlugBuilder;
use WPPluginCore\View\Abstraction\View;

class Menu extends Service
{

    public const TYPE_SUB_MENU = 0;
    public const TYPE_MAIN_MENU = 1;

    private string $slug;
    private string $parentSlug;
    private string $type;
    private string $label;
    private View $view;

    /**
     * @var Menu[]
     */
    private array $subMenuEntries;

    public function __construct(string $slug, string $label, View $view, int $type, string $parentSlug = '', $subMenuEntries = array())
    {
        if (!is_subclass_of($view, View::class) ) {
            throw new IllegalArgumentException("view has to be of type " . View::class);
        }
        if (!($type === self::TYPE_MAIN_MENU || $type === self::TYPE_SUB_MENU)) {
            throw new IllegalArgumentException("The tye ist not " . self::TYPE_MAIN_MENU . " ether " . self::TYPE_SUB_MENU);
        }
        if ($type === self::TYPE_SUB_MENU && $parentSlug === '') {
            throw new IllegalArgumentException('The parentSlug of an sub menu entry can\'t be empty');
        }
        $this->type = $type;
        $this->view = $view;
        $this->label = $label;
        $this->slug = $slug;
        $this->parentSlug = $parentSlug;
        $this->subMenuEntries = $subMenuEntries;
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        parent::registerMe();
        if ($this->type !== self::TYPE_MAIN_MENU) {
            add_action('admin_menu', array($this, 'addMe'));
        } else {
            throw new IllegalStateException("Only Main Menus can be regsiterd");
        }
    }

    public function addMe()
    {
        switch ($this->type) {
            case self::TYPE_MAIN_MENU:
                add_menu_page(
                    $this->label,
                    $this->label,
                    'manage_options',
                    $this->slug,
                    array($this->view, 'show'),
                    "",
                    20
                );
                foreach ($this->subMenuEntries as $menu) {
                    $menu->addMe();
                }
                break;
            case self::TYPE_SUB_MENU:
                add_submenu_page(
                    $this->parentSlug,
                    $this->label,
                    $this->label,
                    'manage_options',
                    $this->slug,
                    array($this->view, 'show')
                );
            default:
                # code...
                break;
        }
    }

    public function addSubMenu(Menu $subMenu) : self
    {
        if ($subMenu->type !== self::TYPE_SUB_MENU) {
            throw new IllegalArgumentException('Submenu should be of type ' . self::TYPE_SUB_MENU .' is actually ' . $subMenu->type);
        }

        array_push($this->subMenuEntries, $subMenu);
        return $this;
        
    }

    public function getSlug() : string
    {
       return $this->slug;
    }
}
