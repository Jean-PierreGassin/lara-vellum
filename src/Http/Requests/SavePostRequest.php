<?php

namespace JeanPierreGassin\LaraVellum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JeanPierreGassin\LaraVellum\Data\SavePostPayload;

class SavePostRequest extends FormRequest
{
    private const int MAX_TITLE_LENGTH = 255;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:'.self::MAX_TITLE_LENGTH,
            ],
            'body' => [
                'required',
                'string',
            ],
            'excerpt' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function toPayload(): SavePostPayload
    {
        return SavePostPayload::fromArray($this->validated());
    }
}
