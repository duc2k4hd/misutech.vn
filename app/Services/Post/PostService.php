<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostService
{
    /**
     * Save a post (Create or Update)
     */
    public function savePost(array $data, $id = null): Post
    {
        return DB::transaction(function () use ($data, $id) {
            if (!empty($data['title']) && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Generate unique slug if changing
            if (!empty($data['slug'])) {
                $baseSlug = $data['slug'];
                $slugCounter = 1;
                $query = Post::where('slug', $data['slug']);
                if ($id) {
                    $query->where('id', '!=', $id);
                }
                while ($query->exists()) {
                    $data['slug'] = $baseSlug . '-' . $slugCounter++;
                    $query = Post::where('slug', $data['slug']);
                    if ($id) {
                        $query->where('id', '!=', $id);
                    }
                }
            }

            // Handle publishing logic based on status
            if (isset($data['status'])) {
                if ($data['status'] === 'published' && empty($data['published_at'])) {
                    $data['published_at'] = Carbon::now();
                } elseif ($data['status'] === 'draft') {
                    $data['published_at'] = null;
                }
            }

            if ($id) {
                $post = Post::findOrFail($id);
                $post->update($data);
            } else {
                $post = Post::create($data);
            }

            // Handle Media relationships
            if (array_key_exists('thumbnail_id', $data)) {
                $this->attachThumbnail($post, $data['thumbnail_id']);
            }

            return $post;
        });
    }

    /**
     * Attach a thumbnail to the post.
     */
    public function attachThumbnail(Post $post, $mediaId)
    {
        // First detach old thumbnail
        $post->thumbnailMedia()->detach();

        if ($mediaId) {
            $post->media()->attach($mediaId, ['role' => 'thumbnail', 'position' => 0]);
        }
    }

    /**
     * Set post status to published
     */
    public function publish(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? Carbon::now()
        ]);
    }

    /**
     * Set post status to draft
     */
    public function unpublish(Post $post)
    {
        $post->update([
            'status' => 'draft',
            'published_at' => null
        ]);
    }
}
