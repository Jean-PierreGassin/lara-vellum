@extends('lara-vellum::dashboard.layout')

@section('content')
    @forelse ($posts as $post)
        <a href="{{ route('lara-vellum.dashboard.edit', $post) }}"
           class="flex items-center justify-between border-b border-stone-200 py-4 hover:bg-stone-50 dark:border-stone-800 dark:hover:bg-stone-900">
            <div>
                <h2 class="font-medium text-stone-900 dark:text-stone-100">{{ $post->title }}</h2>
                <p class="mt-1 text-sm text-stone-400">
                    {{ $post->updated_at?->diffForHumans() }}
                </p>
            </div>
            @if ($post->status->isPublished())
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                    {{ __('lara-vellum::vellum.dashboard.published_label') }}
                </span>
            @else
                <span class="rounded-full bg-stone-200 px-2.5 py-0.5 text-xs font-medium text-stone-600 dark:bg-stone-800 dark:text-stone-300">
                    {{ __('lara-vellum::vellum.dashboard.draft') }}
                </span>
            @endif
        </a>
    @empty
        <p class="py-16 text-center text-stone-400">{{ __('lara-vellum::vellum.dashboard.empty') }}</p>
    @endforelse

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endsection
