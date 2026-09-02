<?php

namespace App\Mcp\Tools;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List job applications, optionally filtered by status.')]
class ListApplicationsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'string',
                Rule::enum(ApplicationStatus::class),
            ]
        ]);

        $applications = Application::query()
            ->with('company')
            ->when($validated['status'] ?? null, fn ($query) => $query->where('status', $validated['status']))
            ->orderByDesc('applications.applied_at')
            ->get();

        $lines = $applications
            ->map(fn (Application $application): string => sprintf(
                '#%d - %s at %s (%s)',
                $application->id,
                $application->role_title,
                $application->company->name,
                $application->status->value,
            ))
            ->implode(PHP_EOL);

        return Response::text($lines !== '' ? $lines : 'No applications found.');
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(array_map(fn (ApplicationStatus $enum): string => $enum->value, ApplicationStatus::cases()))
                ->description('Filter by application status. Omit to list all.'),
        ];
    }
}
