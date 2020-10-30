<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\IDAttribute;

defined('ABSPATH') || exit;

class EntityID extends IDAttribute
{
    private string $intType;

    public function __construct(string $key, string $label, string $intType)
    {
        parent::__construct($key, $label);
        $this->intType = $intType;
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return sprintf('%s UNSIGNED UNIQUE AUTO_INCREMENT', $this->intType);
    }

    public function getAdminHTML(): String
    {
        return $this->createTableInput('
			<input readonly type="number"  id="entity_id" name=' . $this->key . ' value=' . esc_attr(strval($this->getValue())) . ' />
		');
    }
}
