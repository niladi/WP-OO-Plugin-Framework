<?php

namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

defined('ABSPATH') || exit;

use WP_User;

class UserText extends Text
{


    /**
     * @var string
     */
    private $userKey;

    public function __construct(string $key, string $label,string $userKey)
    {
        parent::__construct($key, $label);
        $this->userKey = $userKey;
    }

    /**
     * @param WP_User $user
     *
     * @return bool
     */
    public function userHasKey(WP_User $user)
    {
        return $user->has_prop($this->userKey);
    }

    public function getUserKey(): string
    {
        return $this->userKey;
    }

    public function uploadToUser(WP_User $user): void
    {
        update_user_meta($user->ID, $this->userKey, $this->getValue());
    }
}
