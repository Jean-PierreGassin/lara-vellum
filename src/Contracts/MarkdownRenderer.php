<?php

namespace JeanPierreGassin\LaraVellum\Contracts;

interface MarkdownRenderer
{
    /**
     * Render a Markdown document to a sanitised HTML fragment.
     */
    public function toHtml(string $markdown): string;
}
