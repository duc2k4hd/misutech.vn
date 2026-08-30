<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeriesStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:series,slug',
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'brand_id'         => 'nullable|exists:brands,id',
            'category_id'      => 'nullable|exists:categories,id',
            'sort_order'       => 'nullable|integer',
            'status'           => 'nullable|in:active,draft',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }
}
