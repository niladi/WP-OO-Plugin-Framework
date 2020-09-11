<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;

defined('ABSPATH') || exit;

class Note extends Textarea
{
    public const KEY_NOTES = 'notes'; //Todo wo ist es smarter den key zu haben
    public function __construct()
    {
        parent::__construct(static::KEY_NOTES, __('Notizen', 'wp-plugin-core'));
    }
}
