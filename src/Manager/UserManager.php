<?php

namespace App\Manager;

use App\Entity\User;

class UserManager
{
    /**
     * @param User|null $user
     * @param boolean $superAdmin
     * @return User
     */
    public function create(?User $user = null, $superAdmin = false): User
    {
        $user = $user ?? new User();

        if ($superAdmin) {
            $user->addRole(User::ROLE_SUPER_ADMIN);
        }

        return $user;
    }
}