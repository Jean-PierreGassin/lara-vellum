@extends('lara-vellum::dashboard.layout')

@section('content')
    <nav aria-label="{{ __('lara-vellum::vellum.dashboard.filters.label') }}" class="mb-6 flex flex-wrap gap-1">
        <a href="{{ route('lara-vellum.dashboard.index') }}"
           @if ($filter === null) aria-current="page" @endif
           class="vellum-nav-link @if ($filter === null) vellum-nav-link-active @endif">
            {{ __('lara-vellum::vellum.dashboard.filters.all') }}
            <span class="ms-1 tabular-nums opacity-60">{{ array_sum($counts) }}</span>
        </a>

        @foreach ($statuses as $status)
            <a href="{{ route('lara-vellum.dashboard.index', ['status' => $status->value]) }}"
               @if ($filter === $status) aria-current="page" @endif
               class="vellum-nav-link @if ($filter === $status) vellum-nav-link-active @endif">
                {{ __("lara-vellum::vellum.dashboard.filters.$status->value") }}
                <span class="ms-1 tabular-nums opacity-60">{{ $counts[$status->value] }}</span>
            </a>
        @endforeach
    </nav>

    @if ($posts->isEmpty())
        <div class="vellum-card flex flex-col items-center px-6 py-16 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-stone-100 text-stone-400 dark:bg-stone-800" aria-hidden="true">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5V6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v13.5"/>
                    <path d="M4 19.5A1.5 1.5 0 0 1 5.5 18H19M8 8h7M8 12h7"/>
                </svg>
            </span>

            <p class="mt-4 font-semibold text-stone-900 dark:text-stone-100">
                {{ __('lara-vellum::vellum.dashboard.empty.'.($filter?->value ?? 'all')) }}
            </p>
            <p class="mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">
                {{ __('lara-vellum::vellum.dashboard.empty.hint') }}
            </p>

            <a href="{{ route('lara-vellum.dashboard.create') }}" class="vellum-btn vellum-btn-primary mt-6">
                {{ __('lara-vellum::vellum.dashboard.new_post') }}
            </a>
        </div>
    @else
        <ul class="space-y-3">
            @foreach ($posts as $post)
                <li>
                    <a href="{{ route('lara-vellum.dashboard.edit', $post) }}"
                       class="vellum-card vellum-card-interactive flex items-start gap-4 p-5">
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-semibold tracking-tight text-stone-900 dark:text-stone-100">
                                {{ $post->title }}
                            </h2>

                            <p class="mt-1 line-clamp-2 text-sm text-stone-600 dark:text-stone-400">
                                {{ $post->excerpt ?: __('lara-vellum::vellum.dashboard.no_excerpt') }}
                            </p>

                            <p class="mt-3 text-xs text-stone-500 dark:text-stone-400">
                                {{ __('lara-vellum::vellum.dashboard.stats', ['words' => number_format($post->wordCount()), 'minutes' => $post->readingMinutes()]) }}
                                <span aria-hidden="true" class="mx-1">·</span>
                                {{ __('lara-vellum::vellum.dashboard.edited', ['time' => $post->updated_at?->diffForHumans()]) }}
                            </p>
                        </div>

                        <span class="vellum-badge {{ $post->status->isPublished() ? 'vellum-badge-published' : 'vellum-badge-draft' }}">
                            <span class="vellum-dot" aria-hidden="true"></span>
                            {{ $post->status->label() }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links('lara-vellum::pagination') }}
        </div>
    @endif
@endsection
