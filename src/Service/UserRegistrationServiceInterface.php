<?php

namespace App\Service;

use App\Entity\User;

interface UserRegistrationServiceInterface
{
    /**
     * Create new {@see User} from given data.
     */
    public function createUser(User $user): void;

    public function welcomeEmail(): void;
}
