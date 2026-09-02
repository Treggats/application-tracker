<?php

use App\Enums\ApplicationStatus;

describe('the enum can only move forward', function () {
    test('an interviewing state can not move backward', function () {
        $enum = ApplicationStatus::INTERVIEWING;
        expect($enum->statusChange(ApplicationStatus::OFFER))->toBe(ApplicationStatus::OFFER)
            ->and($enum->statusChange(ApplicationStatus::REJECTED))
                ->toBe(ApplicationStatus::REJECTED)
            ->and($enum->statusChange(ApplicationStatus::WITHDRAWN))
                ->toBe(ApplicationStatus::WITHDRAWN)
            ->and($enum->statusChange(ApplicationStatus::APPLIED))
                ->toBe(ApplicationStatus::INTERVIEWING)
            ->and($enum->statusChange(ApplicationStatus::LEAD))
                ->toBe(ApplicationStatus::INTERVIEWING);
    });

    test('status change must be different than the current', function () {
        $enum = ApplicationStatus::LEAD;
        expect($enum->statusChange(ApplicationStatus::LEAD))->toBe(ApplicationStatus::LEAD);
    });

    test('should follow the order of the status flow', function () {
        $enum = ApplicationStatus::LEAD;
        $applied = $enum->statusChange(ApplicationStatus::APPLIED);
        expect($applied)->toBe(ApplicationStatus::APPLIED);
        $interviewing = $applied->statusChange(ApplicationStatus::INTERVIEWING);
        expect($interviewing)->toBe(ApplicationStatus::INTERVIEWING);
    });

    test('terminal state should stay terminal', function () {
        $enum = ApplicationStatus::OFFER;
        $applied = $enum->statusChange(ApplicationStatus::APPLIED);
        expect($applied)->toBe(ApplicationStatus::OFFER);
    });
});
