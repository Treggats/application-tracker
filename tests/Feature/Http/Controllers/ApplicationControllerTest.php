<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use App\Models\Interaction;

describe('the application controller can create, update and delete applications', function () {
    test('that a list of applications can be retrieved', function () {
        $applications = Application::factory()
            ->count(3)
            ->lead()
            ->create();

        $response = $this->json('GET', route('applications.index'));

        expect($response->content())->toContain(...$applications->pluck('role_title')->all());
    });

    test('that a single application can be retrieved', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->json('GET', route('applications.show', $application));

        expect($response->content())->toContain($application->role_title);
    });

    test('that a single application can be created with the LEAD status', function () {
        $company = Company::factory()->create();

        $response = $this->json('POST', route('applications.store'), [
            'company_id' => $company->id,
            'role_title' => 'employee',
            'source' => 'linkedin',
            'applied_at' => '2026-02-15',
            'notes' => 'seems decent',
        ]);

        $application = Application::query()->firstOrFail();

        $response->assertRedirect(route('applications.show', $application));
    });

    test('that a single application can be updated', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->json('PUT', route('applications.update', $application), [
            'role_title' => 'employee',
        ]);

        $response->assertRedirect(route('applications.show', $application));

        $application->refresh();

        expect($application->role_title)->toBe('employee');
    });

    test('that a status change for an application creates an interaction', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->json('PUT', route('applications.update', $application), [
            'status' => ApplicationStatus::APPLIED->value,
        ]);

        $application->refresh();

        $response->assertRedirect(route('applications.show', $application));

        expect(Interaction::query()->count())->toBe(1);
    });

    test('that a single application can be deleted', function () {
        $application = Application::factory()
            ->lead()
            ->create();

        $response = $this->json('DELETE', route('applications.destroy', $application));

        $response->assertRedirect(route('applications.index'));
        expect(Interaction::query()->count())->toBe(0);
        expect(Application::query()->count())->toBe(0);
    });
});
