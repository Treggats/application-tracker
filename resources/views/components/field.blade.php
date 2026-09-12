@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'rows' => null,
])

@php
    $inputClasses = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100';
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows ?? 4 }}"
            {{ $attributes->merge(['class' => $inputClasses]) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge(['class' => $inputClasses]) }}
        >
    @endif

    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
