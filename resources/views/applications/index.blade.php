<x-layout title="Applications">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Applications</h1>

        <x-button-link variant="primary" href="{{ route('applications.create') }}">
            New application
        </x-button-link>
    </div>

    <div class="mt-6 overflow-hidden overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Company</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Applied</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($applications as $application)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('applications.show', $application) }}" class="font-medium text-gray-900 hover:underline dark:text-gray-100">
                                {{ $application->role_title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $application->company->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$application->status" /></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $application->applied_at?->format('Y-m-d') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No applications yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $applications->links() }}
    </div>
</x-layout>
