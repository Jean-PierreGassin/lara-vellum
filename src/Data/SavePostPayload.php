<?php

namespace JeanPierreGassin\LaraVellum\Data;

final readonly class SavePostPayload
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function __construct(
        public string $title,
        public string $body,
        public ?string $excerpt = null,
        public ?array $meta = null,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            title: $attributes['title'],
            body: $attributes['body'],
            excerpt: $attributes['excerpt'] ?? null,
            meta: $attributes['meta'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'excerpt' => $this->excerpt,
            'meta' => $this->meta,
        ];
    }
}
