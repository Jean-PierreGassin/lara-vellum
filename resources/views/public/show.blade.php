<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }}</title>
    @if ($post->excerpt)
        <meta name="description" content="{{ $post->excerpt }}">
    @endif
</head>
<body>
    <main>
        <article>
            <h1>{{ $post->title }}</h1>
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->toFormattedDateString() }}
                </time>
            @endif
            {!! $content !!}
        </article>
    </main>
</body>
</html>
