<?php


namespace WPPluginCore\Persistence\Domain\Entity\Attribute\Implementation;

defined('ABSPATH') || exit;

class EmailState extends Text
{
    private string $role;

    public function __construct(string $key,string $label,string $role)
    {
        parent::__construct($key, $label);
        $this->role = $role;
    }

    public function getAdminHTML(): String
    {
        $user   = get_user_by('email', $this->getValue());
        $output = '<input type="email" name="' . $this->getKey() . '" value="' . $this->getValue() . '"/><span style="display: block">';
        $output .= __('Nutzer Status:', 'wp-plugin-core') . ' ';
        if (!$user) {
            $output .= __('Kein Nutzer', 'wp-plugin-core');
        } else {
            $output .=  in_array($this->role, $user->roles)
                ? translate_user_role($this->role)
                : __('Normaler Nutzer', 'wp-plugin-core');
        }
        $output.='</span>';
        return $this->createTableInput($output);
    }

    public function validateValue($value): bool
    {
        return parent::validateValue($value) && is_email($value);
    }

    /**
     * @inheritDoc
     */
    protected function getDefault()
    {
        return 'default@mail.de';
    }
}
