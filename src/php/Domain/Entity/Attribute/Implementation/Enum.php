<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;
defined('ABSPATH') || exit;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;

/**
 * @extends Attribute<string>
 * @package WPPluginCore\Domain\Entity\Attribute\Implementation
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
class Enum extends Attribute
{
    private array $values;

    /**
     * MetaStatus constructor.
     *
     * @param $key string of the key
     * @param $label string label of the metadata
     * @param $values string[] states of the Metadate
     *
     * @throws AttributeException if $states is not valid
     */
    public function __construct(string $key, string $label, array $values)
    {
        parent::__construct($key, $label);
        $this->values = $values;
    }

    public function validateValue($value): bool
    {
        return array_key_exists($value, $this->values) ? true : false;
    }

    public function getAdminHTML(): String
    {
        $output = '<select name="'.$this->key.'">';
        foreach ($this->values as $key => $value) {
            $selected = $key == $this->getValue() ? 'selected' : '';
            $output   .= '<option ' . $selected . ' value="' . $key . '">' . $value. '</option>';
        }
        $output .= '</select>';

        return $this->createTableInput($output);
    }

    protected function getDefault()
    {
        return array_keys($this->values)[0];
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return sprintf("ENUM('%s')", implode("', '", array_keys($this->values)));
    }
}
