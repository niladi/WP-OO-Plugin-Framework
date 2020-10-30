<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;
defined('ABSPATH') || exit;
use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\IDAttribute;

class WPPostID extends IDAttribute
{
    /**
     * @var string slug of the post type
     */
    private $slug;

    /**
     * WPPostID constructor.
     *
     * @inheritDoc
     * @param string $slug the slug of the wordpress post
     * @throws AttributeException if slug not exits
     */
    public function __construct(string $key, $label, string $slug)
    {
        parent::__construct($key, $label);
        $this->slug = $slug;
    }

    public function validateValue($value): bool
    {
        return $value == -1 ||  parent::validateValue($value)  && get_post_type($value) == $this->slug; // todo validator 
    }

    public function getDefault()
    {
        return -1;
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return 'INT UNSIGNED UNIQUE';
    }
}
