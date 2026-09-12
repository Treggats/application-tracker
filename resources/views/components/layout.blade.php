@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? "{$title} – " : '' }}{{ config('app.name', 'Laravel') }}</title>

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite('resources/css/app.css')
        @endif
    </head>
    <body class="h-full bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <header class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('applications.index') }}" class="text-sm font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                    Application Tracker
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </body>
</html>
