<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction;

use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalValueException;
use WPPluginCore\Exception\NotSetInPostException;
use WPPluginCore\Exception\ParserException;
use WPPluginCore\Logger;

defined('ABSPATH') || exit;


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
     * @var mixed
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
        $this->label    = $label;
    }


    /**
     * Returns the html for the admin metabox
     *
     * @return String html for the metabox
     */
    abstract public function getAdminHTML(): String;

    /**
     * returns setted temp value
     */
    public function getValue()
    {
        if (! isset($this->value)) {
            try {
                $this->setValue($this->getDefault());
            } catch (IllegalValueException $exception) {
                Logger::error('The default value of attribute with key: ' .$this->key . ', is not valid');
                exit;
            }
        }
        return $this->value;
    }

    /**
     * sets custom meta Value after validates it
     *
     * @param mixed new LS_MI_ value
     *
     * @throws IllegalValueException if the metavalue is not valid
     */
    public function setValue($value) : void
    {
        try {
            $val = isset($value) ? (is_string($value) ?  $this->parseFromString($value) : $value) : $this->getDefault();
        } catch (ParserException $exception) {
            throw new IllegalValueException($exception->getMessage());
        }

        if ($this->validateValue($val)) {
            $this->value = $val;
        } else {
            throw new IllegalValueException(var_export($val). ': is not valid for ' . $this->key);
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
            Logger::info('The Value of ' . $this->label . ' not set in POST variable. Took the default value');
            $this->setValue($this->getDefault());
        }
    }


    /**
     * Validates of the form of the value is correct
     *
     * @param $value mixed is the the meta value you want to check
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
