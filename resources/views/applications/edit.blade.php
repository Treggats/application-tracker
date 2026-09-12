<x-layout :title="'Edit ' . $application->role_title">
    <h1 class="text-2xl font-semibold tracking-tight">Edit application</h1>

    <x-form-errors />

    <form method="POST" action="{{ route('applications.update', $application) }}" class="mt-6 max-w-lg space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        @csrf
        @method('PUT')

        <x-field name="role_title" label="Role title" :value="$application->role_title" />

        <x-field name="source" label="Source" :value="$application->source" />

        <x-field name="applied_at" label="Applied at" type="date" :value="$application->applied_at?->format('Y-m-d')" />

        <x-field name="notes" label="Notes" type="textarea" :value="$application->notes" />

        <div class="flex gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
            <x-button type="submit" variant="primary">
                Save changes
            </x-button>
            <x-button-link variant="secondary" href="{{ route('applications.show', $application) }}">
                Cancel
            </x-button-link>
        </div>
    </form>
</x-layout>
