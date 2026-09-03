<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ApplicationStatus;
use InvalidArgumentException;

final class InvalidStatusTransitionException extends InvalidArgumentException
{
    public static function from(ApplicationStatus $from, ApplicationStatus $to): self
    {
        return new self(sprintf('Cannot transition from %s to %s.', $from->value, $to->value));
    }
}
