@extends('lara-vellum::public.layout')

@section('title', $post->title)

@if ($post->excerpt)
    @section('description', $post->excerpt)
@endif

@section('content')
    <article>
        <header class="mb-10">
            <h1 class="text-4xl font-semibold tracking-tight text-stone-900 dark:text-stone-100">
                {{ $post->title }}
            </h1>
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toIso8601String() }}"
                      class="mt-4 block text-sm text-stone-400">
                    {{ $post->published_at->toFormattedDateString() }}
                </time>
            @endif
        </header>

        <div class="vellum-prose">
            {!! $content !!}
        </div>
    </article>
@endsection

@section('footer')
    {{ $post->published_at?->format('Y') }}
@endsection
