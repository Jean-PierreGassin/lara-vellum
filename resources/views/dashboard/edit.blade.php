@extends('lara-vellum::dashboard.layout')

@section('title', $post->title)

@section('content')
    <form method="POST" action="{{ route('lara-vellum.dashboard.update', $post) }}">
        @csrf
        @method('PUT')

        @include('lara-vellum::dashboard._editor', ['post' => $post])

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-white hover:bg-stone-700 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white">
                {{ $saveLabel }}
            </button>

            @if ($post->isPublished())
                <a href="{{ $publicUrl }}"
                   class="text-sm text-stone-500 hover:text-stone-800 dark:hover:text-stone-200">
                    {{ __('lara-vellum::vellum.dashboard.view') }}
                </a>
            @endif

            <span class="flex-1"></span>

            @if ($post->status->isPublished())
                <button type="submit" form="vellum-unpublish"
                        class="rounded-md px-3 py-2 text-sm font-medium text-stone-500 hover:text-stone-800 dark:hover:text-stone-200">
                    {{ __('lara-vellum::vellum.editor.unpublish') }}
                </button>
            @else
                <button type="submit" form="vellum-publish"
                        class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
                    {{ __('lara-vellum::vellum.editor.publish') }}
                </button>
            @endif

            <button type="submit" form="vellum-delete"
                    data-vellum-confirm="{{ __('lara-vellum::vellum.editor.delete_confirm') }}"
                    class="rounded-md px-3 py-2 text-sm font-medium text-red-600 hover:text-red-500">
                {{ __('lara-vellum::vellum.editor.delete') }}
            </button>
        </div>
    </form>

    <form id="vellum-publish" method="POST" action="{{ route('lara-vellum.dashboard.publish', $post) }}" hidden>@csrf</form>
    <form id="vellum-unpublish" method="POST" action="{{ route('lara-vellum.dashboard.unpublish', $post) }}" hidden>@csrf</form>
    <form id="vellum-delete" method="POST" action="{{ route('lara-vellum.dashboard.destroy', $post) }}" hidden>
        @csrf
        @method('DELETE')
    </form>
@endsection
