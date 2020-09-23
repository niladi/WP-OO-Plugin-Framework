<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\JSONAttribute;

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
     * @inheritDoc
     */
    public function validateValue($value): bool
    {
        return is_null($value) || is_object($value) || ((is_array($value) && (sizeof($value) == 0 || array_keys($value) !== range(0, count($value) - 1))));
    }
}
