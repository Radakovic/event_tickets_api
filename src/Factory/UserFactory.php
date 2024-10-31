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
        'ROLE_USER',
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ){
    }

    public static function class(): string
    {
        return User::class;
    }
    protected function defaults(): array|callable
    {
        $email = self::faker()->email();
        $firstName = self::faker()->firstName();
        $lastName = self::faker()->lastName();
        $roles[] = self::faker()->randomElement(self::ROLES);
        $plainPassword = 'secret';

        $user = new User(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            roles: $roles,
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        return [
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'password' => $hashedPassword,
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
