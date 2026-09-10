<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ApplicationController
{
    public function index(): View
    {
        $applications = Application::query()->paginate();

        return view('applications.index', ['applications' => $applications]);
    }

    public function show(Application $application): View
    {
        return view('applications.show', ['application' => $application]);
    }

    public function create(): View
    {
        return view('applications.create');
    }

    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $application = Application::query()
            ->create([
                ...$request->safe()->except('status'),
                'status' => ApplicationStatus::LEAD->value,
            ]);

        return redirect()->route('applications.show', ['application' => $application]);
    }

    public function edit(Application $application): View
    {
        return view('applications.edit', ['application' => $application]);
    }

    public function update(UpdateApplicationRequest $request, Application $application): RedirectResponse
    {
        $validated = $request->safe()->except('status');
        $application->fill($validated);
        $request->whenEnum('status', ApplicationStatus::class,
            callback: fn (ApplicationStatus $enum) => $application->transitionTo($enum),
            default: fn () => $application->save());

        return redirect()->route('applications.show', ['application' => $application]);
    }

    public function destroy(Application $application): RedirectResponse
    {
        $application->interactions()->delete();
        $application->delete();

        return redirect()->route('applications.index');
    }
}
