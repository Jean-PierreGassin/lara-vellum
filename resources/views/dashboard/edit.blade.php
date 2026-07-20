@extends('lara-vellum::dashboard.layout')

@section('title', $post->title)

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <a href="{{ route('lara-vellum.dashboard.index') }}" class="vellum-btn vellum-btn-ghost -ms-3.5">
            <svg class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 6l-6 6 6 6"/>
            </svg>
            {{ __('lara-vellum::vellum.editor.back') }}
        </a>

        <span class="vellum-badge {{ $post->status->isPublished() ? 'vellum-badge-published' : 'vellum-badge-draft' }}">
            <span class="vellum-dot" aria-hidden="true"></span>
            {{ $post->status->label() }}
        </span>

        @if ($post->isPublished())
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="vellum-btn vellum-btn-ghost text-xs">
                {{ __('lara-vellum::vellum.dashboard.view') }}
                <svg class="vellum-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M8 16 16 8M9 8h7v7"/>
                </svg>
            </a>
        @endif
    </div>

    <form method="POST" action="{{ route('lara-vellum.dashboard.update', $post) }}">
        @csrf
        @method('PUT')

        @include('lara-vellum::dashboard._editor', ['post' => $post])

        <div class="sticky bottom-4 z-20 mt-6">
            <div class="vellum-card flex flex-wrap items-center gap-2 p-3">
                <button type="submit" class="vellum-btn vellum-btn-primary">
                    {{ $saveLabel }}
                </button>

                @if ($post->status->isPublished())
                    <button type="submit" form="vellum-unpublish" class="vellum-btn vellum-btn-secondary">
                        {{ __('lara-vellum::vellum.editor.unpublish') }}
                    </button>
                @else
                    <button type="submit" form="vellum-publish" class="vellum-btn vellum-btn-success">
                        {{ __('lara-vellum::vellum.editor.publish') }}
                    </button>
                @endif

                <p class="ms-auto hidden text-xs text-stone-400 sm:block dark:text-stone-500">
                    {{ __('lara-vellum::vellum.editor.shortcut') }}
                </p>

                {{-- A <details> disclosure, so the confirmation still works without JavaScript. --}}
                <details data-vellum-confirm class="relative">
                    <summary class="vellum-btn vellum-btn-danger vellum-summary-button">
                        {{ __('lara-vellum::vellum.editor.delete') }}
                    </summary>

                    <div class="vellum-card absolute end-0 bottom-full z-30 mb-2 w-72 p-4 text-start shadow-lg">
                        <p class="text-sm font-medium text-stone-900 dark:text-stone-100">
                            {{ __('lara-vellum::vellum.editor.delete_confirm') }}
                        </p>
                        <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                            {{ __('lara-vellum::vellum.editor.delete_confirm_hint') }}
                        </p>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" data-vellum-confirm-cancel class="vellum-btn vellum-btn-ghost">
                                {{ __('lara-vellum::vellum.editor.delete_cancel') }}
                            </button>
                            <button type="submit" form="vellum-delete" class="vellum-btn vellum-btn-danger-solid">
                                {{ __('lara-vellum::vellum.editor.delete_confirmed') }}
                            </button>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </form>

    <form id="vellum-publish" method="POST" action="{{ route('lara-vellum.dashboard.publish', $post) }}" hidden>@csrf</form>
    <form id="vellum-unpublish" method="POST" action="{{ route('lara-vellum.dashboard.unpublish', $post) }}" hidden>@csrf</form>
    <form id="vellum-delete" method="POST" action="{{ route('lara-vellum.dashboard.destroy', $post) }}" hidden>
        @csrf
        @method('DELETE')
    </form>
@endsection
