<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Domain\Entity\Abstraction\Entity;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\IDAttribute;

defined('ABSPATH') || exit;
class EntityAttribute extends IDAttribute
{
    private string $intType;
    private string $path;

    public function __construct(string $key, string $label, string $path, string $intType)
    {
        $this->path = $path;
        $this->intType = $intType;
        parent::__construct($key, $label);
    }


    public function getAdminHTML(): String
    {
        return $this->createTableInput("<input list='$this->key' name='$this->key' value='{$this->getValue()}'>
            <datalist class='entityAtribute' path='$this->path' id='$this->key'></datalist>");
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return sprintf('%s NOT NULL', $this->intType);
    }
}
