<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isApprovedCreator();
    }

    public function rules(): array
    {
        return [
            'caption' => ['nullable', 'string', 'max:10000'],
            'is_locked' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'media' => ['nullable', 'array'],
            'media.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm'],
        ];
    }
}