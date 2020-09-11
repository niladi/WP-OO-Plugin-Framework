<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction;
defined('ABSPATH') || exit;
use JsonException;
use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Exception\IllegalStateException;
use WPPluginCore\Persistence\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Logger;

abstract class JSONAttribute extends Attribute
{

    /**
     * @inheritDoc
     */
    public function getAdminHTML(): string
    {
        return $this->createTableInput(sprintf(
            '<input type="hidden" class="json-attribute-display" name="%s" value=\'%s\'/>',
            $this->key,
            $this->toString()
        ));
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
        try {
            return "'". $this->toString() ."'";
        } catch (IllegalStateException $e) {
            Logger::error($e->getMessage(), $e->getTrace());
            return "'{}'";
        }
    }

    private function toString() : string
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
