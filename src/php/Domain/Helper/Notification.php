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

    public function __construct(string $message, string $title, string $level = self::LEVEL_INFO)
    {
        $this->message = $message;
        $this->title = $title;
        $this->level = $level;
    }

    public function serialize()
    {
        return json_encode( array (
            self::KEY_MESSAGE => $this->message,
            self::KEY_TITLE => $this->title,
            self::KEY_LEVEL => $this->level
        ));
    }

    public function unserialize($serialized)
    {
        $arr = json_decode($serialized, true);
        $this->message = $arr[self::KEY_MESSAGE];
        $this->title = $arr[self::KEY_TITLE];
        $this->level = $arr[self::KEY_LEVEL];
    }

    public static function fromSerialized(string $serialized) : self 
    {
        $temp = new self('', '');
        $temp.unserialize($serialized);
        return $temp;
    }

    public function getBox()
    {
        return "<div class='notice $this->level is-dismissible'>
        <strong>$this->title</strong>
                     <p>$this->message</p>
                 </div>";
    }

    //ggf serialisierbar
}