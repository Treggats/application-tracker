<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case LEAD = 'lead';
    case APPLIED = 'applied';
    case INTERVIEWING = 'interviewing';
    case OFFER = 'offer'; // terminal
    case REJECTED = 'rejected'; // terminal
    case WITHDRAWN = 'withdrawn'; // terminal
}
