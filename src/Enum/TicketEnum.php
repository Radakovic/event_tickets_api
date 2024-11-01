<?php

namespace App\Enum;

enum TicketEnum: string
{
    case Vip = 'VIP';
    case GroundFloor = 'GROUND FLOOR';
    case Floor = 'FLOOR';
    case Balcony = 'BALCONY';
    case Mezzanine = 'MEZZANINE';
    case BoxSeats = 'BOX SEATS';
    case ClubLevel = 'CLUB LEVEL';
}
