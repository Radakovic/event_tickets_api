<?php

namespace App\Factory;

use App\Entity\Event;
use App\Enum\EventEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Event>
 */
final class EventFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Event::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $eventTypes = [];
        foreach (EventEnum::cases() as $enum) {
            $eventTypes[] = $enum->value;
        }
        return [
            'address' => self::faker()->address(),
            'city' => self::faker()->city(),
            'country' => self::faker()->country(),
            'date' => self::faker()->dateTime(),
            'name' => self::faker()->sentence(),
            'type' => self::faker()->randomElement($eventTypes),
            'description' => self::faker()->sentences(3, true),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Event $event): void {})
        ;
    }
}
