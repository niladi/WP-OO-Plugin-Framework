<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\IDAttribute;


defined('ABSPATH') || exit;

class User extends IDAttribute
{
    protected $role;

    public function __construct($key, $label, $role = 'everybody')
    {
        parent::__construct($key, $label);
        $this->role = $role;
    }

    public function validateValue($value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        if ($value == -1) {
            return true;
        }

        $user = get_userdata($value);
        if ($user) {
            if ($this->role == 'everybody' || in_array($this->role, $user->roles)) {
                return true;
            } else {//vllt return false bessser
                throw new AttributeException('User is not a ' . $this->role);
            }
        } else {
            throw new AttributeException('User is not a ' . $this->role);
        }
    }


    public function getAdminHTML(): String
    {
        $options = '';
        $users   = $this->role == 'everybody' ? get_users() : get_users(array( 'role' => $this->role ));
        foreach ($users as $user) {
            $options .= sprintf('<option value="%s">"%s"</option>', $user->ID, $user->user_email);
        }

        return $this->createTableInput('<input list="' . $this->key . '" name="' . $this->key . '" value="' . $this->getValue() . '"><datalist id="' . $this->key . '">'
                                            . $options . '</datalist>');
    }

    /**
     * @inheritDoc
     */
    protected function getDefault()
    {
        return -1;
    }

    /**
     * @inheritDoc
     */
    public function getDBSetup(): string
    {
        return 'INT UNSIGNED';
    }
}
