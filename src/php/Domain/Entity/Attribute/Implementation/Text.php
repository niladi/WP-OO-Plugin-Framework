<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;

defined('ABSPATH') || exit;

/**
 * @extends Attribute<string>
 * @package WPPluginCore\Domain\Entity\Attribute\Implementation
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
class Text extends Attribute
{
    public function validateValue($value): bool
    {
        return is_string($value) ? true : false;
    }

    public function getAdminHTML(): String
    {
        return $this->createTableInput('
			<input type="text" name=' . $this->key . ' value=' . esc_attr($this->getValue()) . ' />
		');
    }


    /**
     * @inheritDoc
     *
     * @return string
     */
    protected function getDefault()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return 'TEXT';
    }
}
