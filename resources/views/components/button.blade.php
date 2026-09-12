@props(['variant' => 'primary'])

@php
    $styles = match ($variant) {
        'primary' => 'bg-gray-900 text-white hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200',
        'secondary' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800',
        'danger' => 'border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950',
    };
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => "inline-flex items-center justify-center rounded-md px-3.5 py-2 text-sm font-medium transition-colors {$styles}"]) }}>
    {{ $slot }}
</button>
