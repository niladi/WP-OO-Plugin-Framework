<?php



namespace WPPluginCore\Domain\Helper;

defined('ABSPATH') || exit;
use WPPluginCore\Plugin;
use Psr\Log\LoggerInterface;
use WPPluginCore\View\MainView;
use WPPluginCore\Util\SlugBuilder;
use WPPluginCore\View\Abstraction\View;
use WPPluginCore\Service\Abstraction\Service;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Exception\IllegalArgumentException;

class Menu
{

    public const TYPE_SUB_MENU = 0;
    public const TYPE_MAIN_MENU = 1;

    private string $slug;
    private string $parentSlug;
    private int $type;
    private string $label;
    private View $view;

    /**
     * @var Menu[]
     */
    private array $subMenuEntries;

    public function __construct( string $slug, string $label, View $view, int $type, string $parentSlug = '',array $subMenuEntries = array())
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

    public function addMe(): void
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

    public function getSlug() : string
    {
       return $this->slug;
    }
}
