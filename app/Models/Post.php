<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title', 'slug', 'summary', 'content', 'author_id', 'category_id',
        'views_count', 'meta_title', 'meta_description', 'status', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }

    /**
     * Get all media associated with the post.
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'post_media')
            ->withPivot('role', 'position')
            ->withTimestamps();
    }

    /**
     * Get just the thumbnail media.
     */
    public function thumbnailMedia()
    {
        return $this->belongsToMany(Media::class, 'post_media')
            ->wherePivot('role', 'thumbnail')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get content/gallery media.
     */
    public function contentMedia()
    {
        return $this->belongsToMany(Media::class, 'post_media')
            ->wherePivot('role', 'content_image')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position', 'asc');
    }
}