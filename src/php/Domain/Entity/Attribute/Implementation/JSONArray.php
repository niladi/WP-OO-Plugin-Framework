<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\JSONAttribute;

/**
 * @extends JSONAttribute<array>
 * @package WPPluginCore\Domain\Entity\Attribute\Implementation
 * @author Niklas Lakner <niklas.lakner@gmail.com>
 */
class JSONArray extends JSONAttribute
{

    /**
     * @inheritDoc
     *
     * @return array
     *
     * @psalm-return array<empty, empty>
     */
    protected function getDefault()
    {
        return array();
    }

    /**
     * @inheritDoc
     */
    public function validateValue($value): bool
    {
        return is_array($value) && (sizeof($value) == 0 || (array_keys($value) == range(0, count($value) - 1)));
    }
}
