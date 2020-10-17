<?php

namespace WPPluginCore\Domain\Helper;

use Serializable;

defined('ABSPATH') || exit;


/**
 * Undocumented class
 * 
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class Notification implements Serializable 
{
    public const LEVEL_SUCCESS = 'notice-success';
    public const LEVEL_ERROR = 'notice-error';
    public const LEVEL_WARNING = 'notice-warning';
    public const LEVEL_INFO = 'notice-info';



    private const KEY_MESSAGE = 'message';
    private string $message;

    private const KEY_TITLE = 'title';
    private string $title;

    private const KEY_LEVEL = 'level';
    private string $level;

    private const KEY_DISMISSED = 'dissmissed';
    private bool $dismissed;

    public function __construct(string $message, string $title, string $level = self::LEVEL_INFO, bool $dismissed = true)
    {
        $this->message = $message;
        $this->title = $title;
        $this->level = $level;
        $this->dismissed = $dismissed;
    }

    public function serialize($value  = null)
    {
        if ($value === null) {
            $value = $this;
        }
        return json_encode( array (
            self::KEY_MESSAGE =>  $value->message,
            self::KEY_TITLE =>  $value->title,
            self::KEY_LEVEL =>  $value->level,
            self::KEY_DISMISSED => $value->dismissed
        ));
    }

    public function unserialize($serialized)
    {
        $arr = json_decode($serialized, true);
        $this->message = $arr[self::KEY_MESSAGE];
        $this->title = $arr[self::KEY_TITLE];
        $this->level = $arr[self::KEY_LEVEL];
        $this->dismissed = $arr[self::KEY_DISMISSED];
    }

    public static function fromSerialized(string $serialized) : self 
    {
        $temp = new self('', '');
        $temp.unserialize($serialized);
        return $temp;
    }

    public function getBox()
    {
        return "<div class='notice $this->level ${($this->dismissed ? 'is-dismissible' : '')}'>
        <strong>$this->title</strong>
                     <p>$this->message</p>
                 </div>";
    }

    //ggf serialisierbar
}