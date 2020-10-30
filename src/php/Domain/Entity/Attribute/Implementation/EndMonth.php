<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use \DateTime;
use WPPluginCore\Util\Date;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Domain\Entity\Attribute\Implementation\Date as DateAttribute;
defined('ABSPATH') || exit;

class EndMonth extends DateAttribute
{

    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label)
    {
        parent::__construct($key, $label, Date::DATE_MONTH);
    }

    /**
     * @inheritDoc
     */
    public function loadFromPost() : void
    {
        if (isset($_POST[ $this->key ])) {
            $this->setValue(Date::createDateTime($_POST[ $this->key ] . '-31'));
        } else {
            throw new \WPPluginCore\Exception\AttributeException('The Value of ' . $this->label . ' not set in POST variable');
        }
    }


    public function getDefault()
    {
        return Date::getLastDay();
    }


    /**
     * @inheritDoc
     */
    public function validateValue($value): bool
    {
        return parent::validateValue($value) && $value->format('d') == '31';
    }
}
