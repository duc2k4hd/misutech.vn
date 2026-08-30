<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Series extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'content',
        'brand_id', 'category_id', 'sort_order', 'status',
        'meta_title', 'meta_description',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Tất cả sản phẩm thuộc series này, sắp xếp theo tên.
     */
    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('name');
    }

    /**
     * Chỉ các sản phẩm active thuộc series (Tối đa 15 model mới nhất).
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->take(15);
    }
}
