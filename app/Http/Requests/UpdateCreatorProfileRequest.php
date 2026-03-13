<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreatorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isApprovedCreator();
    }

    public function rules(): array
    {
        $profileId = $this->user()->creatorProfile?->id;

        return [
            'display_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:creator_profiles,slug,' . $profileId],
            'bio' => ['nullable', 'string', 'max:5000'],
            'monthly_price' => ['required', 'numeric', 'min:1', 'max:999.99'],
            'allow_tips' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:4096'],
            'banner' => ['nullable', 'image', 'max:6144'],
        ];
    }
}