@php($post = $post ?? null)

<div data-vellum-editor data-preview-url="{{ route('lara-vellum.dashboard.preview') }}">
    <input type="text" name="title" value="{{ old('title', $post?->title) }}"
           placeholder="{{ __('lara-vellum::vellum.editor.title_placeholder') }}"
           class="w-full bg-transparent text-3xl font-semibold tracking-tight text-stone-900 placeholder-stone-300 focus:outline-none dark:text-stone-100 dark:placeholder-stone-600"
           autofocus>
    @error('title')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <input type="text" name="excerpt" value="{{ old('excerpt', $post?->excerpt) }}"
           placeholder="{{ __('lara-vellum::vellum.editor.excerpt_placeholder') }}"
           class="mt-3 w-full bg-transparent text-stone-500 placeholder-stone-300 focus:outline-none dark:placeholder-stone-600">

    <div class="mt-6 flex gap-1 border-b border-stone-200 dark:border-stone-800">
        <button type="button" data-vellum-tab="write"
                class="border-b-2 border-stone-900 px-3 py-2 text-sm font-medium dark:border-stone-100">
            {{ __('lara-vellum::vellum.editor.write') }}
        </button>
        <button type="button" data-vellum-tab="preview"
                class="border-b-2 border-transparent px-3 py-2 text-sm font-medium text-stone-400">
            {{ __('lara-vellum::vellum.editor.preview') }}
        </button>
    </div>

    <textarea name="body" data-vellum-body rows="18"
              placeholder="{{ __('lara-vellum::vellum.editor.body_placeholder') }}"
              class="mt-4 block w-full resize-y bg-transparent font-mono text-sm leading-relaxed text-stone-800 placeholder-stone-300 focus:outline-none dark:text-stone-200 dark:placeholder-stone-600">{{ old('body', $post?->body) }}</textarea>
    @error('body')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div data-vellum-preview hidden class="vellum-prose mt-4 min-h-[16rem]"></div>
</div>
