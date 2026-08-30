<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Media;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'sku', 'price', 'sale_price', 'short_description', 'content',
        'category_id', 'brand_id', 'series_id', 'views_count', 'rating_average',
        'reviews_count', 'meta_title', 'meta_description', 'status', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function reviews() { return $this->hasMany(Review::class); }

    /**
     * Dòng/Series mà sản phẩm này thuộc về.
     */
    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * Get the thumbnail media for the product.
     */
    public function thumbnailMedia()
    {
        return $this->belongsToMany(Media::class, 'product_media')
            ->wherePivot('role', 'thumbnail')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Get the gallery media for the product.
     */
    public function galleryMedia()
    {
        return $this->belongsToMany(Media::class, 'product_media')
            ->wherePivot('role', 'gallery')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Get the catalog media for the product.
     */
    public function catalogMedia()
    {
        return $this->belongsToMany(Media::class, 'product_media')
            ->wherePivot('role', 'catalog')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Backward compatibility or generic all media getter.
     */
    public function images()
    {
        return $this->belongsToMany(Media::class, 'product_media')
            ->withPivot('role', 'position')
            ->orderByPivot('position');
    }

    /**
     * Scope query lấy các sản phẩm đã xuất bản (active & published_at <= now theo giờ Việt Nam).
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'active')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', \Carbon\Carbon::now('Asia/Ho_Chi_Minh'));
    }
}