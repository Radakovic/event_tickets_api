<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsAlias(id: UserRegistrationServiceInterface::class)]
class UserRegistrationService implements UserRegistrationServiceInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
    ){
    }

    /**
     * @inheritDoc
     */
    public function createUser(array $userData): void
    {
        $roles = $userData['roles'] ?? ['ROLE_USER'];

        $user = new User(
            firstName: $userData['firstName'],
            lastName: $userData['lastName'],
            email: $userData['email'],
            roles: $roles,
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userData['password']
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function welcomeEmail(): void
    {
        // Sending welcome email is not related with this assignment
    }
}
