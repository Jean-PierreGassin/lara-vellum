<?php

namespace JeanPierreGassin\LaraVellum\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return (string) __("lara-vellum::vellum.status.$this->value");
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }
}
