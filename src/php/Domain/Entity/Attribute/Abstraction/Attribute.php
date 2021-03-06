<?php


namespace WPPluginCore\Domain\Entity\Attribute\Abstraction;

use WPPluginCore\Logger;
use WPPluginCore\Exception\ParserException;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Exception\IllegalValueException;
use WPPluginCore\Exception\NotSetInPostException;

defined('ABSPATH') || exit;

/**
 * @template T
 * @package WPPluginCore\Domain\Entity\Attribute\Abstraction
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
abstract class Attribute
{
    /**
     * @var string
     */
    protected string $label;
    /**
     * @var string
     */
    protected string $key;
    
    /**
     * @psalm-var T
     */
    protected $value;

    /**
     * Attribute constructor.
     *s
     * @param $key string key of value
     * @param $label string for display
     */
    public function __construct(string $key, string $label)
    {
        $this->key = $key;
        $this->label = $label;
        $this->value = $this->getDefault();
    }


    /**
     * Returns the html for the admin metabox
     *
     * @return String html for the metabox
     */
    abstract public function getAdminHTML(): String;

    /**
     * returns setted temp value
     * 
     * @psalm-return T
     */
    public function getValue()
    {
        if (is_null($this->value)) {
            try {
                $this->setValue($this->getDefault());
            } catch (IllegalValueException $exception) {
                throw new IllegalStateException('The default value of attribute with key: ' .$this->key . ', is not valid');
            }
        }
        return $this->value;
    }

    /**
     * sets custom meta Value after validates it
     *
     * @param T $value
     *
     * @throws IllegalValueException if the metavalue is not valid
     */
    public function setValue($value) : void
    {
        try {
            $val = is_string($value) ?  $this->parseFromString($value) : $value;
        } catch (ParserException $exception) {
            throw new IllegalValueException($exception->getMessage());
        }

        if ($this->validateValue($val)) {
            $this->value = $val;
        } else {
            throw new IllegalValueException(': is not valid for ' . $this->key);
        }
    }

    /**
     * Parses the value from an String input
     *
     * @param string $value the input as an string
     *
     * @return mixed the new value (default the current string)
     */
    protected static function parseFromString(string $value)
    {
        return $value;
    }

    /**
     * it have to match the validate function
     * 
     * @psalm-return T
     */
    abstract protected function getDefault();

    /**
     * Returns the DB Setup String
     *
     * @return string the DB Setup String
     */
    abstract public function getDBSetup() : string ;

    /**
     * Returns the value as string, serialized for DB entry
     *
     * @return string
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function getValueForDB() :string
    {
        $val = $this->getValue();
        return is_null($val) ? 'null' :  "'". strval($val) . "'";
    }

    /**
     * Returns the metakey
     * @return String meta key
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * Loads meta value from post
     * @throws IllegalValueException if the value the variable POST[key] is wrong
     */
    public function loadFromPost() : void
    {
        if (isset($_POST[ $this->key ])) {
            $this->setValue($_POST[ $this->key ]);
        } else {
            $this->setValue($this->getDefault());
        }
    }


    /**
     * Validates of the form of the value is correct
     *
     * @param T $value mixed is the the meta value you want to check
     *
     * @return bool true if is valid otherwise false
     */
    abstract public function validateValue($value): bool;

    /**
     * Creates html input for a metabox table (helper function for getAdminHTML())
     *
     * @param $html String the custom html for the content
     *
     * @return string the new LS_MI_box table row
     */
    protected function createTableInput(string $html) : string
    {
        return '<tr>
		    <th scope="row"><label for=' . $this->key . '>' . $this->label . '</label></th>
		    <td>' . $html . '</td>
		</tr>';
    }
}
