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
    private array $tempNotices;

    /**
     * @var Notification[]
     */
    private array $persistentNotices;

    private const KEY_OPTION = 'notice_option';
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;    
        $this->tempNotices = array();
        $this->persistentNotices = array();
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
            array_push($this->persistentNotices, self::fromSerializedArray(get_option( self::KEY_OPTION)));    
            array_push($this->tempNotices,  self::fromSerializedArray($_GET[self::KEY_OPTION]));
            $this->loaded = true;
        }
    }

    /**
     * Undocumented function
     *
     * @param array $arr
     * @return Notification[]
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private static function fromSerializedArray(array $arr) : array
    {
        return array_map(fn(string $element) => Notification::fromSerialized($element),$arr);
    }

    /**
     * Undocumented function
     *
     * @param Notification[] $arr
     * @return array
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    private static function toSerializedArray(array $arr) : array
    {
        return array_map(fn(Notification $element) => $element->serialize(), $arr);
    }

    public function registerSetting() : void
    {
        register_setting(self::KEY_OPTION, self::KEY_OPTION);
    }

    public function addPersistent(Notification $notification) : void 
    {
        $this->load();
        array_push($this->persistentNotices, $notification);
        update_option( self::KEY_OPTION, self::toSerializedArray($this->persistentNotices) );
    }

    public function addTemp(Notification $notification) : void 
    {
        $this->load();
        array_push($this->tempNotices, $notification);
    }

    public function show(): void
    {
        $this->load();
        foreach($this->notices as $notification) {
            echo $notification->getBox();
        }
    }
}