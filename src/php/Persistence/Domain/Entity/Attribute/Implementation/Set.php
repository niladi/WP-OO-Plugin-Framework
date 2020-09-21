<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;
defined('ABSPATH') || exit;
use WPPluginCore\Logger;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction\Attribute;

class Set extends Attribute
{
    /**
     * @var array|string[]
     */
    private $values;

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
        if (is_array($values)) {
            parent::__construct($key, $label);
            $this->values = $values;
        } else {
            throw new AttributeException('$states is not a array');
        }
    }

    public function validateValue($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if (! array_key_exists($val, $this->values)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function addValue($value)
    {
        if (! array_key_exists($value, $this->values)) {
            throw new AttributeException('This Value '. $value. ' is not in Values');
        }
        array_push($this->value, $value);
    }

    public function removeValue($value)
    {
        if (! array_key_exists($value, $this->values)) {
            throw new AttributeException('This Value '. $value. ' is not in Values');
        }
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

    /**
     * @inheritDoc
     */
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
