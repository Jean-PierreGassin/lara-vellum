<?php

namespace JeanPierreGassin\LaraVellum\Support;

use Illuminate\Support\Str;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;

class CommonMarkRenderer implements MarkdownRenderer
{
    /**
     * @var array<string, mixed>
     */
    private const array COMMONMARK_OPTIONS = [
        'html_input' => 'escape',
        'allow_unsafe_links' => false,
    ];

    public function toHtml(string $markdown): string
    {
        return Str::markdown(
            string: $markdown,
            options: self::COMMONMARK_OPTIONS,
        );
    }
}
