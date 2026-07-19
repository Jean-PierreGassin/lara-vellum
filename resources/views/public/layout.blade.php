<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @hasSection('description')
        <meta name="description" content="@yield('description')">
    @endif
    <link rel="stylesheet" href="{{ route('lara-vellum.assets.css') }}">
</head>
<body class="min-h-full bg-stone-50 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    <div class="mx-auto flex min-h-full max-w-2xl flex-col px-6">
        <main class="flex-1 py-16 sm:py-24">
            @yield('content')
        </main>
        <footer class="border-t border-stone-200 py-8 text-sm text-stone-400 dark:border-stone-800">
            @yield('footer')
        </footer>
    </div>
</body>
</html>
