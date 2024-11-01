<?php

namespace App\Doctrine\EnumType;

use App\Enum\EventEnum;

class EventEnumType extends AbstractEnumType
{
    protected string $name = 'event_type';
    protected array $values = [
        EventEnum::Carnival->value,
        EventEnum::Fair->value,
        EventEnum::Concert->value,
        EventEnum::Festival->value,
        EventEnum::Parade->value,
        EventEnum::TheaterPerformance->value,
    ];
}
