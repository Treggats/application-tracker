<x-layout title="New application">
    <h1 class="text-2xl font-semibold tracking-tight">New application</h1>

    <x-form-errors />

    <form method="POST" action="{{ route('applications.store') }}" class="mt-6 max-w-lg space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        @csrf

        {{-- TODO: replace with company lookup-or-create once that flow exists --}}
        <x-field name="company_id" label="Company ID" type="number" />

        <x-field name="role_title" label="Role title" />

        <x-field name="source" label="Source" />

        <x-field name="applied_at" label="Applied at" type="date" />

        <x-field name="notes" label="Notes" type="textarea" />

        <div class="flex gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
            <x-button type="submit" variant="primary">
                Create application
            </x-button>
            <x-button-link variant="secondary" href="{{ route('applications.index') }}">
                Cancel
            </x-button-link>
        </div>
    </form>
</x-layout>
