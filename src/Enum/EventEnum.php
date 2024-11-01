<?php

namespace App\Enum;

enum EventEnum: string
{
    case Festival = 'FESTIVAL';
    case Concert = 'CONCERT';
    case Carnival = 'CARNIVAL';
    case TheaterPerformance = 'THEATER_PERFORMANCE';
    case Fair = 'FAIR';
    case Parade = 'PARADE';
}
