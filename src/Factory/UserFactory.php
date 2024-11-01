<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private const ROLES = [
        'ROLE_ADMIN',
        'ROLE_MANAGER',
        'ROLE_USER',
    ];

    public static function class(): string
    {
        return User::class;
    }
    protected function defaults(): array|callable
    {
        $roles[] = self::faker()->randomElement(self::ROLES);

        // Password for all generated users is `secret`
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'password' => '$2y$13$h8C3aoTWltYiyp2q0uzuC.1db5RGYABX76wC3j4c5M4S1DK85iRT6',
            'roles' => $roles,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
