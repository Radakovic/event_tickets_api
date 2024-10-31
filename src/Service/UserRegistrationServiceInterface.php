<?php

namespace App\Service;

use App\Entity\User;

interface UserRegistrationServiceInterface
{
    /**
     * Create new {@see User} from given data.
     * @param array<string, string> $userData
     */
    public function createUser(array $userData): void;

    public function welcomeEmail(): void;
}
