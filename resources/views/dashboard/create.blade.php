@extends('lara-vellum::dashboard.layout')

@section('title', __('lara-vellum::vellum.dashboard.new_post'))

@section('content')
    <form method="POST" action="{{ route('lara-vellum.dashboard.store') }}">
        @csrf

        @include('lara-vellum::dashboard._editor')

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="vellum-btn vellum-btn-primary">
                {{ __('lara-vellum::vellum.editor.save_draft') }}
            </button>

            <a href="{{ route('lara-vellum.dashboard.index') }}" class="vellum-btn vellum-btn-ghost">
                {{ __('lara-vellum::vellum.editor.back') }}
            </a>

            <p class="ms-auto text-xs text-stone-500 dark:text-stone-400">
                {{ __('lara-vellum::vellum.editor.draft_hint') }}
            </p>
        </div>
    </form>
@endsection
