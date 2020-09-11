<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;
defined('ABSPATH') || exit;
use WPPluginCore\DBInit;
use WPPluginCore\Logger;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction\IDAttribute;

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
        if (!post_type_exists($slug)) {
            if (! DBInit::getInstance()->onInit()) {
                    throw new AttributeException('slug is not registred');
            }
        }
        parent::__construct($key, $label);
        $this->slug = $slug;
    }

    public function validateValue($value): bool
    {
        return $value == -1 ||  parent::validateValue($value)  && get_post_type($value) == $this->slug;
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
