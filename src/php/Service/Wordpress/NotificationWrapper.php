<?php


namespace WPPluginCore\Service\Wordpress;
defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use WPPluginCore\Domain\Helper\Notification;
use WPPluginCore\Plugin;
use WPPluginCore\Service\Abstraction\Service;


class NotificationWrapper extends Service
{
    private LoggerInterface $logger;

    private bool $loaded;

    /**
     * @var Notification[]
     */
    private array $notices;

    private const KEY_OPTION = 'notice_option';
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;    
        $this->notices = array();
        $this->loaded = false;
    }

    public function registerMe(): void
    {
        add_action('admin_notices', array($this,'show'));
        add_action('admin_init', array($this, 'registerSetting'));
        //todo register Setting
    }

    private function load() : void 
    {
        if (!$this->loaded) {
            array_push($this->notices, 
                fn($element) => Notification::fromSerialized($element),get_option( self::KEY_OPTION));    
            $this->loaded = true;
        }
    }

    private function save() : void
    {
        update_option( self::KEY_OPTION, $this->notices );
    }

    public function registerSetting() : void
    {
        register_setting(self::KEY_OPTION, self::KEY_OPTION);
    }

    public function add(Notification $notification) : void 
    {
        $this->load();
        array_push($this->notices, $notification);
        $this->save();
    }

    public function show(): void
    {
        $this->load();
        foreach($this->notices as $notification) {
            echo $notification->getBox();
        }
    }
}