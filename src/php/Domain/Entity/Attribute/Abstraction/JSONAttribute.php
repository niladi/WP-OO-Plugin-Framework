<?php


namespace WPPluginCore\Domain\Entity\Attribute\Abstraction;
defined('ABSPATH') || exit;
use JsonException;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Logger;

abstract class JSONAttribute extends Attribute
{

    /**
     * @inheritDoc
     */
    public function getAdminHTML(): string
    {
        return $this->createTableInput("<input type='hidden' class='json-attribute-display' name='$this->key' value='$this'/>");
    }

    protected static function parseFromString(string $value)
    {
        try {
            $val = str_replace("\\", "", $value);
            return  json_decode($val, true, 512, JSON_THROW_ON_ERROR) ;
        } catch (JsonException $e) {
            throw new AttributeException('JSON error occurs');
        }
    }


    /**
     * @inheritDoc
     */
    public function getValueForDB() : string
    {
        return "'". $this ."'";
    }

    public function __toString() : string
    {
        $val = $this->getValue();
        try {
            if (is_null($val)) {
                return '{}';
            } else {
                return  json_encode($val, JSON_THROW_ON_ERROR);
            }
        } catch (JsonException $e) {
            new IllegalStateException('Cant serialize the data to json string');
        }
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return 'TEXT';
    }
}
