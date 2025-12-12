<?php

namespace App\Enums;

enum OrderStatus: string
{
    case OPEN = 'OPEN';
    case FILLED = 'FILLED';
    case CANCELLED = 'CANCELLED';
}