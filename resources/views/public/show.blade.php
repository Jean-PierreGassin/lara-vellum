@extends('lara-vellum::public.layout')

@section('title', $post->title)

@if ($post->excerpt)
    @section('description', $post->excerpt)
@endif

@section('content')
    <article>
        <header class="mb-10">
            <h1 class="text-3xl font-semibold tracking-tight text-balance text-stone-900 sm:text-4xl dark:text-stone-100">
                {{ $post->title }}
            </h1>

            @if ($post->excerpt)
                <p class="mt-4 text-lg leading-relaxed text-stone-600 dark:text-stone-400">
                    {{ $post->excerpt }}
                </p>
            @endif

            <p class="mt-6 flex flex-wrap items-center gap-x-2 text-sm text-stone-500 dark:text-stone-400">
                @if ($post->published_at)
                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                        {{ $post->published_at->toFormattedDateString() }}
                    </time>
                    <span aria-hidden="true">·</span>
                @endif
                <span>{{ __('lara-vellum::vellum.post.reading_time', ['minutes' => $post->readingMinutes()]) }}</span>
            </p>
        </header>

        <div class="vellum-prose">
            {!! $content !!}
        </div>
    </article>
@endsection

@section('footer')
    {{ $post->published_at?->format('Y') }}
@endsection
