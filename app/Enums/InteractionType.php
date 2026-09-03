<?php

declare(strict_types=1);

namespace App\Enums;

enum InteractionType: string
{
    case NOTE = 'note';
    case STATUS_CHANGE = 'status_change';
    case EMAIL = 'email';
    case CALL = 'call';
    case INTERVIEW = 'interview';
}
