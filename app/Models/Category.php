<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'slug', 'parent_id', 'type', 'icon', 'banner', 'position', 'status', 'content'];

    public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }
    public function products() { return $this->hasMany(Product::class); }
    public function posts() { return $this->hasMany(Post::class); }

    /**
     * Lấy toàn bộ bản đồ danh mục con cháu (In-memory tree mapping & cached)
     */
    public static function getDescendantsMap($type = 'product')
    {
        return \Illuminate\Support\Facades\Cache::remember("category_descendants_map_{$type}", 3600, function () use ($type) {
            $all = self::where('type', $type)
                ->where('status', 'active')
                ->select(['id', 'slug', 'parent_id'])
                ->get();

            $childrenMap = [];
            $slugToId = [];
            foreach ($all as $c) {
                $pId = $c->parent_id ?: 0;
                $childrenMap[$pId][] = $c->id;
                $slugToId[$c->slug] = $c->id;
            }

            $getDescendants = function ($parentId) use (&$getDescendants, &$childrenMap) {
                $ids = [$parentId];
                if (isset($childrenMap[$parentId])) {
                    foreach ($childrenMap[$parentId] as $childId) {
                        $ids = array_merge($ids, $getDescendants($childId));
                    }
                }
                return $ids;
            };

            $descendantMap = [];
            foreach ($all as $c) {
                $descendantMap[$c->id] = $getDescendants($c->id);
            }

            return [
                'descendants' => $descendantMap,
                'slug_to_id' => $slugToId,
            ];
        });
    }

    /**
     * Lấy danh sách ID của chính danh mục này và tất cả danh mục con cháu cấp dưới (In-memory siêu tốc, 0 query).
     */
    public function getAllChildrenIds()
    {
        $map = self::getDescendantsMap($this->type ?: 'product');
        if (isset($map['descendants'][$this->id])) {
            return $map['descendants'][$this->id];
        }

        // Fallback an toàn
        $ids = [$this->id];
        $children = self::where('parent_id', $this->id)->pluck('id')->toArray();
        foreach ($children as $cId) {
            $childCat = self::find($cId);
            if ($childCat) {
                $ids = array_merge($ids, $childCat->getAllChildrenIds());
            }
        }
        return array_values(array_unique($ids));
    }
}