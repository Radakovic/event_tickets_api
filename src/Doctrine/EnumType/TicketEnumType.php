<?php

namespace App\Doctrine\EnumType;

use App\Enum\TicketEnum;

class TicketEnumType extends AbstractEnumType
{
    protected string $name = 'ticket_type';
    protected array $values = [
        TicketEnum::Balcony->value,
        TicketEnum::BoxSeats->value,
        TicketEnum::Floor->value,
        TicketEnum::ClubLevel->value,
        TicketEnum::Vip->value,
        TicketEnum::GroundFloor->value,
        TicketEnum::Mezzanine->value,
    ];
}
