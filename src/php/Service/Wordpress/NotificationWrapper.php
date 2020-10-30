<?php


namespace WPPluginCore\Service\Wordpress;
defined('ABSPATH') || exit;

use Psr\Log\LoggerInterface;
use Roave\BetterReflection\Reflection\Exception\FunctionDoesNotExist;
use WPPluginCore\Domain\Helper\Notification;
use WPPluginCore\Plugin;
use WPPluginCore\Service\Abstraction\Service;


class NotificationWrapper extends Service
{

    private bool $loaded;

    /**
     * @var Notification[]
     */
    private array $tempNotices;

    /**
     * @var Notification[]
     */
    private array $persistentNotices;

    private const KEY_OPTION_GROUP = 'notice_option';
    private const KEY_NOTICE_PERSISTENT = 'no_persistent';
    private const KEY_NOTICE_TEMP = 'no_temp';

    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->loaded = false;
        $this->tempNotices = array();
        $this->persistentNotices = array();
    }
    


    public function __destruct()
    {
        if ($this->loaded) {
            update_option( self::KEY_NOTICE_PERSISTENT, self::toSerializedArray($this->persistentNotices) );
            update_option( self::KEY_NOTICE_TEMP, self::toSerializedArray($this->tempNotices) );
        }
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
            array_push($this->persistentNotices,  ...self::getFromOption(self::KEY_NOTICE_PERSISTENT));
            array_push($this->tempNotices,  ...self::getFromOption(self::KEY_NOTICE_TEMP));
            $this->loaded = true;
        }
    }

    /**
     * @return Notification[]
     *
     * @psalm-return array<array-key, Notification>
     */
    private static function getFromOption(string $optionKey) : array
    {
        $notices = get_option( $optionKey);
        return self::fromSerializedArray(is_array($notices) ? $notices : array());
    }

    /**
     * Undocumented function
     *
     * @param array $arr
     *
     * @return Notification[]
     *
     * @author Niklas Lakner niklas.lakner@gmail.com
     *
     * @psalm-return list<Notification>
     */
    private static function fromSerializedArray(array $arr) : array
    {
        $ret = array();
        foreach ($arr as $key => $value) {
            array_push($ret, Notification::fromJSON($value));
        }
        return $ret;
    }

    /**
     * Undocumented function
     *
     * @param Notification[] $arr
     *
     * @return array
     *
     * @author Niklas Lakner niklas.lakner@gmail.com
     *
     * @psalm-return list<mixed>
     */
    private static function toSerializedArray(array $arr) : array
    {
        $ret = array();
        foreach ($arr as $key => $value) {
            array_push($ret, $value->toJSON());
        }
        return $ret;
    }

    public function registerSetting() : void
    {
        register_setting(self::KEY_OPTION_GROUP, self::KEY_NOTICE_PERSISTENT);
        register_setting(self::KEY_OPTION_GROUP, self::KEY_NOTICE_TEMP);
    }

    public function addPersistent(Notification $notification) : void 
    {
        $this->load();
        array_push( $this->persistentNotices, $notification );
    }
    
    public function addTemp(Notification $notification) : void 
    {
        $this->load();
        array_push($this->tempNotices, $notification );
    }


    public function show(): void
    {
        $this->load();
        foreach($this->tempNotices as $notification) {
            echo $notification->getBox();
        }
        $this->tempNotices = array();
        foreach($this->persistentNotices as $notification) {
            echo $notification->getBox();
        }
    }
}