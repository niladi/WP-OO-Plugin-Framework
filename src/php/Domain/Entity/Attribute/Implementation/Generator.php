<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;
use WPPluginCore\Domain\Entity\Abstraction\WPEntity;

defined('ABSPATH') || exit;


class Generator extends Attribute
{
    protected $create_function;

    protected $delete_function;

    public function __construct($key, $label, $create_function, $delete_function = '')
    {
        parent::__construct($key, $label);
        $this->create_function = $create_function;
        $this->delete_function = $delete_function;
    }

    public function getAdminHTML(): String
    {
        if ($this->value) {
            return $this->createTableInput('<input type="text" name=' . $this->key . ' value=' . esc_attr($this->getValue()) . ' />' .
                                              ($this->delete_function == ''  ? '' : WPEntity::createAjaxButton($this->delete_function, __('Löschen', 'wp-plugin-core'))));
        } else {
            return $this->createTableInput(WPEntity::createAjaxButton($this->create_function, __('Erstellen', 'wp-plugin-core')));
        }
    }

    /**
     * @return true
     */
    public function validateValue($value): bool
    {
        return true;
    }

    public function loadFromPost()
    {
        if (isset($_POST[ $this->key ])) {
            $this->setValue($_POST[ $this->key ]);
        }
    }


    /**
     * @inheritDoc
     *
     * @return string
     */
    protected function getDefault()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return 'TEXT';
    }
}
