<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('lara-vellum::vellum.dashboard.title'))</title>
    <link rel="stylesheet" href="{{ route('lara-vellum.assets.css') }}">
</head>
<body class="min-h-full bg-stone-100 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    <div class="mx-auto max-w-4xl px-6">
        <header class="flex items-center justify-between border-b border-stone-200 py-6 dark:border-stone-800">
            <a href="{{ route('lara-vellum.dashboard.index') }}" class="flex items-baseline gap-2">
                <span class="text-lg font-semibold tracking-tight text-stone-900 dark:text-stone-100">
                    {{ __('lara-vellum::vellum.dashboard.title') }}
                </span>
                <span class="text-sm text-stone-400">{{ __('lara-vellum::vellum.dashboard.tagline') }}</span>
            </a>
            <a href="{{ route('lara-vellum.dashboard.create') }}"
               class="rounded-md bg-stone-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-stone-700 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white">
                {{ __('lara-vellum::vellum.dashboard.new_post') }}
            </a>
        </header>

        @if (session('status'))
            <div class="mt-6 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <main class="py-10">
            @yield('content')
        </main>
    </div>

    <script src="{{ route('lara-vellum.assets.js') }}" defer></script>
</body>
</html>
