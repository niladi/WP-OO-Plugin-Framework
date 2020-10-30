<?php


namespace WPPluginCore\Domain\Entity\Attribute\Abstraction;

use WPPluginCore\Util\Parser;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalValueException;
use WPPluginCore\Exception\IllegalArgumentException;

defined('ABSPATH') || exit;

/**
 * @extends Attribute<int>
 * @package WPPluginCore\Domain\Entity\Attribute\Abstraction
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
abstract class IDAttribute extends Attribute
{
    public function getAdminHTML(): String
    {
        return $this->createTableInput('
			<input readonly type="number"  name=' . $this->key . ' value=' . esc_attr($this->getValue()) . ' />
		');
    }

    public function validateValue($value): bool
    {
        return is_int($value) && $value >= -1;
    }

    /**
     * @inheritDoc
     *
     * @return int
     */
    protected function getDefault()
    {
        return -1;
    }

    protected static function parseFromString(string $value)
    {
        if (strtolower($value) === 'null') {
            return -1;
        } else {
            try {
                return Parser::strToInt($value);
            } catch (\Exception $ex) {
                throw new IllegalValueException("String not Parsable to int \n" . $ex->getMessage());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getValueForDB(): string
    {
        return $this->getValue() == -1 ? 'null' : strval($this->getValue());
    }
}
