<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class TransitionToStatusRule implements ValidationRule
{
    public function __construct(
        private Application $application,
    ) {
        //
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $originalStatus = $this->application->status;
        /** @phpstan-var int|string $value */
        $newStatus = ApplicationStatus::tryFrom($value);

        if (! $newStatus instanceof ApplicationStatus) {
            $fail("Application status '{$value}' is not a valid application status");

            return;
        }

        if ($originalStatus->canTransitionTo($newStatus)) {
            return;
        }

        $fail(sprintf('Cannot transition to status %s from %s status', $newStatus->value, $originalStatus->value));
    }
}
