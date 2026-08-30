<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            // brand_id và series_id có thể để trống hoặc điền tên mới để tự động tạo khi thêm mới
            'brand_id' => 'nullable',
            'series_id' => 'nullable',
            'status' => 'nullable|in:active,draft',
            'short_description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            
            // Media
            'thumbnail_id' => 'nullable|integer|exists:media,id',
            'gallery_ids' => 'nullable|array',
            'gallery_ids.*' => 'integer|exists:media,id',
            'catalog_ids' => 'nullable|array',
            'catalog_ids.*' => 'integer|exists:media,id',
        ];
    }
}
