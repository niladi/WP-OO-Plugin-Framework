<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Exception\ParserException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Exception\AttributeException;
use \DateTime;
defined('ABSPATH') || exit;

class Date extends Attribute
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $format;

    /**
     * Date constructor.
     *
     * @inheritDoc
     * @param $format string the date format
     */
    public function __construct(string $key, string $label, string $format)
    {
        parent::__construct($key, $label);
        $this->format = $format;
        if ($this->format == \WPPluginCore\Util\Date::DATE_MONTH) {
            $this->type = 'month';
        } else {
            $this->type = 'date';
        }
    }

    /**
     * @inheritDoc
     */
    public function validateValue($value): bool
    {
        return $value instanceof DateTime;
    }

    /**
     * @param string $value parses a string value to data
     *
     * @return DateTime the new DateTime
     * @throws ParserException if an error ocurs
     */
    protected static function parseFromString(string $value)
    {
        try {
            return new DateTime($value);
        } catch (\Exception $e) {
            throw new ParserException($e->getMessage());
        }
    }


    /**
     * @inheritDoc
     */
    public function getAdminHTML(): String
    {

        return $this->createTableInput(
            '<input type="' . $this->type . '" class="' . $this->key . '"
                   name="' . $this->key . '" value="' . $this->getValue()->format($this->format). '"/>'
        );
    }

    /**
     * @inheritDoc
     *
     * @return DateTime
     */
    public function getDefault()
    {
        return new DateTime();
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup() : string
    {
        return 'DATE NOT NULL';
    }

    /**
     * @inheritDoc
     */
    public function getValueForDB(): string
    {
        return $this->getValue() ?  "'" .  $this->getValue()->format('Y-m-d') . "'" : 'null';
    }

}
