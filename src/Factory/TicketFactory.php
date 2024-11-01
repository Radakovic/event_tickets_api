<?php

namespace App\Factory;

use App\Entity\Ticket;
use App\Enum\TicketEnum;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Ticket>
 */
final class TicketFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Ticket::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $ticketTypes = [];
        foreach (TicketEnum::cases() as $enum) {
            $ticketTypes[] = $enum->value;
        }
        return [
            //'createdAt' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'numberAvailableTickets' => self::faker()->randomNumber(4),
            'price' => self::faker()->randomNumber(4),
            'type' => self::faker()->randomElement($ticketTypes),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Ticket $ticket): void {})
        ;
    }
}
