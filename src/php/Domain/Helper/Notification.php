<?php

namespace WPPluginCore\Domain\Helper;

use Serializable;

defined('ABSPATH') || exit;


/**
 * Undocumented class
 * 
 * @author Niklas Lakner niklas.lakner@gmail.com
 */
class Notification
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

    /**
     * @return false|string
     */
    public function toJSON()
    {
        return json_encode( array (
            self::KEY_MESSAGE =>  $this->message,
            self::KEY_TITLE =>  $this->title,
            self::KEY_LEVEL =>  $this->level,
            self::KEY_DISMISSED => $this->dismissed
        ));
    }

    public static function fromJSON(string $serialized) : self
    {
        $arr = json_decode($serialized, true);
        return new self($arr[self::KEY_MESSAGE], $arr[self::KEY_TITLE], $arr[self::KEY_LEVEL], $arr[self::KEY_DISMISSED]);
    }

    public function getBox(): string
    {
        $dismissed = ($this->dismissed ? 'is-dismissible' : '');
        return "<div class='notice $this->level $dismissed'>
        <strong>$this->title</strong>
                     <p>$this->message</p>
                 </div>";
    }

    //ggf serialisierbar
}