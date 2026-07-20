<?php

namespace JeanPierreGassin\LaraVellum\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }
}
