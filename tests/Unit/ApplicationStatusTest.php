<?php

use App\Enums\ApplicationStatus;

describe('the enum can only move forward', function () {
    test('an interviewing state can not move backward', function () {
        $enum = ApplicationStatus::INTERVIEWING;
        expect($enum->canTransitionTo(ApplicationStatus::REJECTED))->toBeTrue();
    });
});
