<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Domain\Entity\Abstraction\Entity;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\IDAttribute;

defined('ABSPATH') || exit;
class EntityAttribute extends IDAttribute
{
    private $getValid;
    private string $intType; //todo für das maybe entity zu trait (in implementierung)

    public function __construct(string $key, string $label, callable $getValid, string $intType)
    {
        $this->getValid = $getValid;
        $this->intType = $intType;
        parent::__construct($key, $label);
    }

    private function getValidPost() : array
    {
        if ($this->getValid) {
            $result = call_user_func($this->getValid);
            if ($result) {
                return $result;
            }
        }
        return array();
    }

    public function getAdminHTML(): String
    {

// BUtton bzw. reflink hinzufuegen
        $options = '';
        foreach ($this->getValidPost() as $entity) {
            if (! $entity instanceof Entity) {
                throw new AttributeException(var_export($entity) . "\n is not an entity");
            }
            $options .= '<option value="' . $entity->getID() . '"> ' . $entity->getID() . ' </option>';
        }

        return $this->createTableInput('<input list="' . $this->key . '" name="' . $this->key . '" value="' . $this->getValue() . '"><datalist id="' . $this->key . '">'
                                            . $options . '</datalist>');
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return sprintf('%s NOT NULL', $this->intType);
    }
}
