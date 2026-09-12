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

    test('that a validation error is thrown when the status is not a known status', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->putJson(route('applications.update', $application), [
            'status' => 'invalid',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid(['status'])
            ->assertJsonValidationErrors([
                'status' => [
                    'The selected status is invalid.',
                    'Application status \'invalid\' is not a valid application status',
                ],
            ]);
    });

    test('that a validation error is thrown when the status is not allowed to progress', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->putJson(route('applications.update', $application), [
            'status' => ApplicationStatus::INTERVIEWING,
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid(['status'])
            ->assertJsonValidationErrors([
                'status' => [
                    'Cannot transition to status interviewing from lead status',
                ],
            ]);
    });
});
