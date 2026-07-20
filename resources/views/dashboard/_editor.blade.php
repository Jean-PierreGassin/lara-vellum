@php($post = $post ?? null)

<div data-vellum-editor data-preview-url="{{ route('lara-vellum.dashboard.preview') }}">
    <div class="vellum-card p-5 sm:p-7">
        <label for="vellum-title" class="sr-only">{{ __('lara-vellum::vellum.editor.title_label') }}</label>
        <input type="text" id="vellum-title" name="title" value="{{ old('title', $post?->title) }}"
               placeholder="{{ __('lara-vellum::vellum.editor.title_placeholder') }}"
               required
               @error('title') aria-invalid="true" aria-describedby="vellum-title-error" @enderror
               class="vellum-input text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl dark:text-stone-100"
               autofocus>
        @error('title')
            <p id="vellum-title-error" class="vellum-error">{{ $message }}</p>
        @enderror

        <label for="vellum-excerpt" class="sr-only">{{ __('lara-vellum::vellum.editor.excerpt_label') }}</label>
        <input type="text" id="vellum-excerpt" name="excerpt" value="{{ old('excerpt', $post?->excerpt) }}"
               placeholder="{{ __('lara-vellum::vellum.editor.excerpt_placeholder') }}"
               class="vellum-input mt-3 text-stone-600 dark:text-stone-400">
        @error('excerpt')
            <p class="vellum-error">{{ $message }}</p>
        @enderror

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 dark:border-stone-800">
            <div role="tablist" data-vellum-tablist hidden aria-label="{{ __('lara-vellum::vellum.editor.views') }}" class="flex gap-1">
                <button type="button" role="tab" data-vellum-tab="write" id="vellum-tab-write"
                        aria-controls="vellum-pane-write" aria-selected="true"
                        class="vellum-tab vellum-tab-active">
                    {{ __('lara-vellum::vellum.editor.write') }}
                </button>
                <button type="button" role="tab" data-vellum-tab="preview" id="vellum-tab-preview"
                        aria-controls="vellum-pane-preview" aria-selected="false" tabindex="-1"
                        class="vellum-tab">
                    {{ __('lara-vellum::vellum.editor.preview') }}
                </button>
                <button type="button" role="tab" data-vellum-tab="split" id="vellum-tab-split"
                        aria-controls="vellum-pane-preview" aria-selected="false" tabindex="-1"
                        class="vellum-tab hidden lg:block">
                    {{ __('lara-vellum::vellum.editor.split') }}
                </button>
            </div>

            <p data-vellum-stats
               data-template="{{ __('lara-vellum::vellum.editor.stats', ['words' => ':words', 'minutes' => ':minutes']) }}"
               class="pb-2 text-xs text-stone-500 tabular-nums dark:text-stone-400">
                {{ __('lara-vellum::vellum.editor.stats', ['words' => number_format($post?->wordCount() ?? 0), 'minutes' => $post?->readingMinutes() ?? 1]) }}
            </p>
        </div>

        <div data-vellum-panes class="mt-4 grid gap-6">
            <div data-vellum-pane="write" id="vellum-pane-write" role="tabpanel" aria-labelledby="vellum-tab-write">
                <label for="vellum-body" class="sr-only">{{ __('lara-vellum::vellum.editor.body_label') }}</label>
                <textarea id="vellum-body" name="body" data-vellum-body rows="18"
                          placeholder="{{ __('lara-vellum::vellum.editor.body_placeholder') }}"
                          required
                          @error('body') aria-invalid="true" aria-describedby="vellum-body-error" @enderror
                          class="vellum-input field-sizing-content block min-h-80 resize-y font-vellum-mono text-sm leading-relaxed text-stone-800 dark:text-stone-200">{{ old('body', $post?->body) }}</textarea>
                @error('body')
                    <p id="vellum-body-error" class="vellum-error">{{ $message }}</p>
                @enderror
            </div>

            <div data-vellum-pane="preview" id="vellum-pane-preview" role="tabpanel" aria-labelledby="vellum-tab-preview" hidden>
                <div data-vellum-preview aria-live="polite"
                     data-empty-label="{{ __('lara-vellum::vellum.editor.preview_empty') }}"
                     data-error-label="{{ __('lara-vellum::vellum.editor.preview_error') }}"
                     class="vellum-prose min-h-80"></div>
            </div>
        </div>
    </div>
</div>
