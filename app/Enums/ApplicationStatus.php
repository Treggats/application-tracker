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

    private function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return false;
        }

        if ($this === self::INTERVIEWING) {
            return in_array($target, [self::OFFER, self::REJECTED, self::WITHDRAWN], strict: true);
        }

        if ($this === self::APPLIED && in_array($target, [self::INTERVIEWING, self::REJECTED, self::WITHDRAWN], strict: true)) {
            return true;
        }

        return ($this === self::LEAD) && ($target === self::APPLIED);
    }

    public function statusChange(self $status): self
    {
        if (! $this->canTransitionTo($status)) {
            return $this;
        }

        return $status;
    }
}
