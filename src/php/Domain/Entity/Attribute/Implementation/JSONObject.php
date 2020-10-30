<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\JSONAttribute;

/**
 * @extends JSONAttribute<?object>
 * @package WPPluginCore\Domain\Entity\Attribute\Implementation
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
class JSONObject extends JSONAttribute
{

    /**
     * @inheritDoc
     */
    protected function getDefault()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateValue($value): bool
    {
        return true;//is_null($value) || is_object($value) || ((is_array($value) && (sizeof($value) == 0 || array_keys($value) !== range(0, count($value) - 1))));
    }
}
