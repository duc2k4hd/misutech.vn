<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'disk',
        'folder',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'width',
        'height',
        'checksum',
        'title',
        'alt',
        'notes',
        'status',
    ];

    protected $casts = [
        'size'   => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
    ];

    protected $appends = [
        'url',
        'relative_path'
    ];

    // ─────────────────── Accessors ───────────────────

    /**
     * The relative path from storage root, e.g. 'clients/imgs/products/abc.jpg'
     */
    public function getRelativePathAttribute(): string
    {
        return $this->folder
            ? rtrim($this->folder, '/') . '/' . $this->filename
            : $this->filename;
    }

    /**
     * Web-accessible URL (hoặc Direct External URL nếu là link ngoài)
     */
    public function getUrlAttribute(): string
    {
        if ($this->disk === 'external' || str_starts_with($this->filename, 'http://') || str_starts_with($this->filename, 'https://')) {
            return $this->filename;
        }

        return $this->relative_path
            ? asset('storage/' . $this->relative_path)
            : '';
    }

    /**
     * Absolute path on disk: public/storage/clients/imgs/products/abc.jpg
     */
    public function getAbsolutePathAttribute(): string
    {
        return public_path('storage/' . $this->relative_path);
    }

    /**
     * Whether this is an image file.
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Human-readable file size.
     */
    public function getSizeHumanAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1_048_576)  return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1_048_576, 2) . ' MB';
    }

    /**
     * Whether the file exists on disk.
     */
    public function existsOnDisk(): bool
    {
        return file_exists($this->absolute_path);
    }

    // ─────────────────── Scopes ───────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInFolder(Builder $query, string $folder): Builder
    {
        return $query->where('folder', $folder);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('filename', 'like', "%{$term}%")
              ->orWhere('original_name', 'like', "%{$term}%")
              ->orWhere('title', 'like', "%{$term}%")
              ->orWhere('alt', 'like', "%{$term}%");
        });
    }

    // ─────────────────── Helpers ───────────────────

    /**
     * Convert model to API array format.
     */
    public function toApiArray(): array
    {
        return [
            'id'            => $this->id,
            'folder'        => $this->folder,
            'filename'      => $this->filename,
            'original_name' => $this->original_name,
            'extension'     => $this->extension,
            'mime_type'     => $this->mime_type,
            'size'          => (int) $this->size,
            'size_human'    => $this->size_human,
            'width'         => $this->width,
            'height'        => $this->height,
            'title'         => $this->title ?? '',
            'alt'           => $this->alt ?? '',
            'notes'         => $this->notes ?? '',
            'status'        => $this->status,
            'is_image'      => $this->is_image,
            'url'           => $this->url,
            'relative_path' => $this->relative_path,
            'created_at'    => $this->created_at?->format('d/m/Y H:i') ?? '',
            'updated_at'    => $this->updated_at?->format('d/m/Y H:i') ?? '',
        ];
    }
    /**
     * Get products that use this media.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_media')
            ->withPivot('role', 'position')
            ->withTimestamps();
    }

    /**
     * Get posts that use this media.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_media')
            ->withPivot('role', 'position')
            ->withTimestamps();
    }
}
