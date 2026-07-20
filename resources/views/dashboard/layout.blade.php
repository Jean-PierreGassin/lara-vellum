<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('lara-vellum::vellum.dashboard.title'))</title>
    <link rel="stylesheet" href="{{ $stylesheetUrl }}">
    {{-- Loaded ahead of the body so a saved theme applies before the first paint. --}}
    <script src="{{ $scriptUrl }}"></script>
</head>
<body class="min-h-full bg-stone-100 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    <a href="#vellum-content" class="vellum-skip-link">{{ __('lara-vellum::vellum.dashboard.skip') }}</a>

    <header class="sticky top-0 z-30 border-b border-stone-200 bg-stone-100/85 backdrop-blur-sm dark:border-stone-800 dark:bg-stone-950/85">
        <div class="mx-auto flex max-w-5xl items-center gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('lara-vellum.dashboard.index') }}" class="flex items-center gap-2.5 rounded-lg">
                <span class="flex size-8 items-center justify-center rounded-lg bg-stone-900 text-sm font-semibold text-white dark:bg-stone-100 dark:text-stone-900" aria-hidden="true">V</span>
                <span class="flex flex-col leading-tight">
                    <span class="text-sm font-semibold tracking-tight text-stone-900 dark:text-stone-100">
                        {{ __('lara-vellum::vellum.dashboard.title') }}
                    </span>
                    <span class="hidden text-xs text-stone-500 sm:block dark:text-stone-400">
                        {{ __('lara-vellum::vellum.dashboard.tagline') }}
                    </span>
                </span>
            </a>

            <div class="ms-auto flex items-center gap-2">
                <button type="button" data-vellum-theme-toggle hidden aria-pressed="false"
                        class="vellum-btn vellum-btn-ghost px-2">
                    <span class="sr-only">{{ __('lara-vellum::vellum.dashboard.theme') }}</span>
                    <svg data-vellum-theme-icon="light" class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                    </svg>
                    <svg data-vellum-theme-icon="dark" class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" hidden aria-hidden="true">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>
                    </svg>
                </button>

                <a href="{{ route('lara-vellum.dashboard.create') }}" class="vellum-btn vellum-btn-primary">
                    <svg class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    {{ __('lara-vellum::vellum.dashboard.new_post') }}
                </a>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        <div aria-live="polite">
            @if (session('status'))
                <div data-vellum-flash role="status"
                     class="mt-6 flex items-start gap-3 rounded-xl border border-emerald-600/20 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                    <svg class="vellum-icon mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m5 13 4 4L19 7"/>
                    </svg>
                    <p class="flex-1">{{ session('status') }}</p>
                    <button type="button" data-vellum-dismiss hidden class="rounded-md p-0.5 hover:opacity-70">
                        <span class="sr-only">{{ __('lara-vellum::vellum.dashboard.dismiss') }}</span>
                        <svg class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        <main id="vellum-content" tabindex="-1" class="py-8 sm:py-10">
            @yield('content')
        </main>
    </div>
</body>
</html>
