<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction\Attribute;

defined('ABSPATH') || exit;

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
