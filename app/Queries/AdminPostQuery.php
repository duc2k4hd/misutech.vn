<?php

namespace App\Queries;

use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostQuery
{
    /**
     * Get the Datatables response for the admin post list.
     */
    public function getDatatables(Request $request)
    {
        try {
            $trashStatus = $request->input('trash_status', 'all'); // 'all', 'active', 'trashed'
            $query = Post::withTrashed()->with(['category', 'author', 'thumbnailMedia']);

            if ($trashStatus === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($trashStatus === 'trashed') {
                $query->onlyTrashed();
            }

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $totalRecords = Post::withTrashed()->count();
            $filteredRecords = $query->count();

            $perPage = (int) $request->input('length', 10);
            if ($perPage === -1) {
                $perPage = $filteredRecords > 0 ? $filteredRecords : 10;
            } elseif ($perPage <= 0) {
                $perPage = 10;
            }

            $start = max(0, (int) $request->input('start', 0));
            $page = max(1, (int) ($start / $perPage) + 1);

            $posts = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Thêm cờ `is_trashed` cho mỗi item
            $items = collect($posts->items())->map(function($p) {
                $arr = $p->toArray();
                $arr['is_trashed'] = $p->trashed();
                return $arr;
            })->values()->all();

            return [
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $items,
                'counts' => [
                    'all'     => Post::withTrashed()->count(),
                    'active'  => Post::whereNull('deleted_at')->count(),
                    'trashed' => Post::onlyTrashed()->count(),
                ]
            ];
        } catch (\Throwable $e) {
            \Log::error('Post getDatatables error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'counts' => ['all' => 0, 'active' => 0, 'trashed' => 0],
                'error' => $e->getMessage()
            ];
        }
    }
}
