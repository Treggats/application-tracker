<x-layout :title="$application->role_title">
    <x-form-errors />

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{{ $application->role_title }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $application->company->name }}</p>
            </div>
            <x-status-badge :status="$application->status" class="text-sm" />
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-6 text-sm sm:grid-cols-4">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Source</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $application->source ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Applied</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $application->applied_at?->format('Y-m-d') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Company city</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $application->company->city ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Company website</dt>
                <dd class="mt-1">
                    @if ($application->company->website)
                        <a
                            href="{{ $application->company->website }}"
                            class="text-blue-600 hover:underline dark:text-blue-400"
                            target="_blank"
                            rel="noopener"
                        >
                            {{ $application->company->website }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-gray-100">—</span>
                    @endif
                </dd>
            </div>
        </dl>

        @if ($application->notes)
            <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-800">
                <h2 class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Notes</h2>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $application->notes }}</p>
            </div>
        @endif

        <div class="mt-6 flex gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <x-button-link variant="secondary" href="{{ route('applications.edit', $application) }}">
                Edit
            </x-button-link>

            <form method="POST" action="{{ route('applications.destroy', $application) }}" onsubmit="return confirm('Delete this application?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">
                    Delete
                </x-button>
            </form>
        </div>
    </div>

    @php
        $nextStatuses = array_filter(
            \App\Enums\ApplicationStatus::cases(),
            fn (\App\Enums\ApplicationStatus $status) => $application->status->canTransitionTo($status),
        );
    @endphp

    @if ($nextStatuses !== [])
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Change status</h2>
            <form method="POST" action="{{ route('applications.update', $application) }}" class="mt-3 flex items-center gap-3">
                @csrf
                @method('PUT')
                <select
                    name="status"
                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                >
                    @foreach ($nextStatuses as $status)
                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="primary">
                    Update status
                </x-button>
            </form>
        </div>
    @endif

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Contacts</h2>
        <div class="mt-3 space-y-3">
            @forelse ($application->company->contacts as $contact)
                <div class="text-sm">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $contact->name }}</span>
                    @if ($contact->role)
                        <span class="text-gray-500 dark:text-gray-400">— {{ $contact->role }}</span>
                    @endif
                    @if ($contact->email)
                        <span class="ml-2 text-gray-500 dark:text-gray-400">{{ $contact->email }}</span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No contacts yet.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Timeline</h2>
        <ul class="mt-3 space-y-4">
            @forelse ($application->interactions->sortBy('occurred_at') as $interaction)
                <li class="border-l-2 border-gray-200 pl-4 text-sm dark:border-gray-700">
                    <div class="flex items-baseline gap-2">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $interaction->type->value)) }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $interaction->occurred_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if ($interaction->body)
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $interaction->body }}</p>
                    @endif
                </li>
            @empty
                <li class="text-sm text-gray-500 dark:text-gray-400">No interactions yet.</li>
            @endforelse
        </ul>
    </div>
</x-layout>
