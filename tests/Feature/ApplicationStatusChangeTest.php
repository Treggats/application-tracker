<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Application;
use App\Models\Interaction;

describe('the status of an application can change', function () {
    test('an interaction is created when the status of an application is updated', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $application->transitionTo(ApplicationStatus::APPLIED);

        /** @var Interaction $interaction */
        $interaction = $application->interactions()->first();

        expect(Interaction::query()->count())
            ->toBe(1)
            ->and($interaction->type->value)
            ->toBe('status_change');
    });

    test('an exception is thrown when the application status is prohibited', function () {
        $application = Application::factory()
            ->interviewing()
            ->create();

        expect(fn () => $application->transitionTo(ApplicationStatus::APPLIED))
            ->toThrow(InvalidStatusTransitionException::class);

        expect($application->status)->toBe(ApplicationStatus::INTERVIEWING);

        expect(Interaction::query()->count())->toBe(0);
    });

});
