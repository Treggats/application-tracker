@props(['status'])

@php
    $styles = match ($status) {
        \App\Enums\ApplicationStatus::LEAD => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-300/20',
        \App\Enums\ApplicationStatus::APPLIED => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/40 dark:text-blue-300 dark:ring-blue-300/20',
        \App\Enums\ApplicationStatus::INTERVIEWING => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/40 dark:text-amber-300 dark:ring-amber-300/20',
        \App\Enums\ApplicationStatus::OFFER => 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-900/40 dark:text-green-300 dark:ring-green-300/20',
        \App\Enums\ApplicationStatus::REJECTED => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/40 dark:text-red-300 dark:ring-red-300/20',
        \App\Enums\ApplicationStatus::WITHDRAWN => 'bg-gray-100 text-gray-500 ring-gray-600/15 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-300/15',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$styles}"]) }}>
    {{ ucfirst($status->value) }}
</span>
