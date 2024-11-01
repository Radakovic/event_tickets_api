<?php

namespace App\Factory;

use App\Entity\Organizer;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Organizer>
 */
final class OrganizerFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Organizer::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'address' => self::faker()->streetAddress(),
            'city' => self::faker()->city(),
            'createdAt' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'name' => self::faker()->company(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Organizer $organizer): void {})
        ;
    }
}
