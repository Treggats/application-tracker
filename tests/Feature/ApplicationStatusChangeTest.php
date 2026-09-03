<?php

declare(strict_types=1);

use App\Models\Application;
use App\Models\Interaction;

describe('the status of an application can change', function () {
    test('an interaction is created when the status of an application is updated', function () {
        $application = Application::factory()
            ->lead()
            ->create();
        $application->update(['status' => 'applied']);

        /** @var Interaction $interaction */
        $interaction = $application->interactions()->first();

        expect(Interaction::query()->count())
            ->toBe(1)
            ->and($interaction->type->value)
            ->toBe('status_change');
    });
});
