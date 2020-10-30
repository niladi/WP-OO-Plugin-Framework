<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;
defined('ABSPATH') || exit;
use WPPluginCore\Logger;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Exception\IllegalArgumentException;

/**
 * @extends Attribute<array>
 * 
 * @package WPPluginCore\Domain\Entity\Attribute\Implementation
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
class Set extends Attribute
{
    /**
     * @var string[]
     */
    private $values;

    /**
     * MetaStatus constructor.
     *
     * @param $key string of the key
     * @param $label string label of the metadata
     * @param $values string[] states of the Metadate
     *
     */
    public function __construct(string $key, string $label, array $values)
    {
        parent::__construct($key, $label);
        $this->values = $values;
    }

    public function validateValue($value): bool
    {
        foreach ($value as $val) {
            if (! array_key_exists($val, $this->values)) {
                return false;
            }
        }
        return true;
    }

    public function addValue(string $value): void
    {
        if (! array_key_exists($value, $this->values)) {
            throw new IllegalArgumentException('This Value '. $value. ' is not in Values');
        }
        array_push($this->value, $value);
    }

    public function removeValue(string $value): void
    {
        if (! array_key_exists($value, $this->values)) {
            throw new IllegalArgumentException('This Value '. $value. ' is not in Values');
        }
        $this->value = \array_filter($this->value, fn($val) => $value !== $val);
        // todo
    }

    public function getAdminHTML(): String
    {
        $output = '';
        foreach ($this->values as $key => $value) {
            $checked = false;
            foreach ($this->getValue() as $val) {
                if ($key == $val) {
                    $checked = true;
                }
            }
            $output   .= sprintf(' <input type="checkbox" name="%s[]" value="%s" %s />%s<br />', $this->key, $key, $checked ? 'checked' : '', $value);
        }
        return $this->createTableInput($output);
    }

    protected function getDefault()
    {
        return array();
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return sprintf("SET('%s')", implode("', '", array_keys($this->values)));
    }

    /**
     * @inheritDoc
     */
    public function getValueForDB(): string
    {
        return "'". implode(',', $this->getValue()) . "'";
    }

    protected static function parseFromString(string $value)
    {
        return empty($value)? array() : explode(",", $value);
    }


}
