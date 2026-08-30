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
        if ($perPage == -1) {
            $perPage = $filteredRecords > 0 ? $filteredRecords : 1;
        }

        $start = (int) $request->input('start', 0);
        $page = ($perPage > 0) ? ($start / $perPage) + 1 : 1;

        $posts = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Thêm cờ `is_trashed` cho mỗi item
        $items = collect($posts->items())->map(function($p) {
            $arr = $p->toArray();
            $arr['is_trashed'] = $p->trashed();
            return $arr;
        });

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $items,
            'counts' => [
                'all'     => Post::withTrashed()->count(),
                'active'  => Post::whereNull('deleted_at')->count(),
                'trashed' => Post::onlyTrashed()->count(),
            ]
        ];
    }
}
