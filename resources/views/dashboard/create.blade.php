@extends('lara-vellum::dashboard.layout')

@section('title', __('lara-vellum::vellum.dashboard.new_post'))

@section('content')
    <form method="POST" action="{{ route('lara-vellum.dashboard.store') }}">
        @csrf

        @include('lara-vellum::dashboard._editor')

        <div class="mt-8 flex items-center gap-3">
            <button type="submit"
                    class="rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-white hover:bg-stone-700 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white">
                {{ __('lara-vellum::vellum.editor.save_draft') }}
            </button>
            <a href="{{ route('lara-vellum.dashboard.index') }}" class="text-sm text-stone-400 hover:text-stone-600">
                {{ __('lara-vellum::vellum.editor.back') }}
            </a>
        </div>
    </form>
@endsection
