<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\Brand;
use App\Models\Category;

class DocumentController extends Controller
{
    /**
     * Hiển thị trang Trung tâm Tài liệu kỹ thuật & Catalog.
     * Chỉ lấy các tài liệu thực sự được liên kết với sản phẩm đang hoạt động (active, chưa xóa).
     */
    public function index(Request $request)
    {
        $search = trim($request->input('q', ''));
        $brandSlug = $request->input('brand');
        $catSlug = $request->input('category');
        $fileType = $request->input('type', 'all');

        // Chỉ lấy các media có role là 'catalog' gắn với sản phẩm đang hoạt động
        $query = Media::query()
            ->select(['id', 'filename', 'original_name', 'disk', 'folder', 'mime_type', 'extension', 'size', 'created_at', 'title', 'notes'])
            ->whereHas('products', function ($pQuery) {
                $pQuery->where('product_media.role', 'catalog')
                       ->where('products.status', 'active');
            })
            ->with(['products' => function ($p) {
                $p->where('products.status', 'active')
                  ->with(['brand:id,name,slug', 'category:id,name,slug', 'series:id,name,slug'])
                  ->select('products.id', 'products.name', 'products.slug', 'products.sku', 'products.brand_id', 'products.category_id', 'products.series_id');
            }]);

        // Lọc theo từ khóa tìm kiếm (tên file, tiêu đề tài liệu, tên sản phẩm, mã SKU)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('products', function ($p) use ($search) {
                      $p->where('products.status', 'active')
                        ->where(function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhereHas('brand', function ($b) use ($search) {
                                    $b->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('category', function ($c) use ($search) {
                                    $c->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('series', function ($s) use ($search) {
                                    $s->where('name', 'like', "%{$search}%");
                                });
                        });
                  });
            });
        }

        // Lọc theo Thương hiệu
        if (!empty($brandSlug)) {
            $query->whereHas('products', function ($p) use ($brandSlug) {
                $p->where('products.status', 'active')
                  ->whereHas('brand', function ($b) use ($brandSlug) {
                      $b->where('slug', $brandSlug);
                  });
            });
        }

        // Lọc theo Danh mục
        if (!empty($catSlug)) {
            $query->whereHas('products', function ($p) use ($catSlug) {
                $p->where('products.status', 'active')
                  ->whereHas('category', function ($c) use ($catSlug) {
                      $c->where('slug', $catSlug);
                  });
            });
        }

        // Lọc theo Loại file
        if ($fileType === 'pdf') {
            $query->where(function ($q) {
                $q->where('extension', 'pdf')->orWhere('mime_type', 'like', '%pdf%');
            });
        }

        $documents = $query->latest('id')->paginate(12)->withQueryString();

        // Danh sách Thương hiệu và Danh mục để lọc (Cached)
        $brandsData = \Illuminate\Support\Facades\Cache::remember('document_filter_brands', 3600, function () {
            return Brand::select(['id', 'name', 'slug'])
                ->whereHas('products', function ($p) {
                    $p->where('status', 'active')->whereHas('catalogMedia');
                })
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });
        $brands = collect($brandsData)->map(fn($item) => (object)$item);

        $categoriesData = \Illuminate\Support\Facades\Cache::remember('document_filter_categories', 3600, function () {
            return Category::select(['id', 'name', 'slug'])
                ->where('status', 'active')
                ->where('type', 'product')
                ->whereHas('products', function ($p) {
                    $p->where('status', 'active')->whereHas('catalogMedia');
                })
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });
        $categories = collect($categoriesData)->map(fn($item) => (object)$item);

        // Đếm tổng số tài liệu thực tế của các sản phẩm đang active (Cached)
        $totalDocsCount = \Illuminate\Support\Facades\Cache::remember('document_total_count', 3600, function () {
            return Media::whereHas('products', function ($pQuery) {
                $pQuery->where('product_media.role', 'catalog')
                       ->where('products.status', 'active');
            })->count();
        });

        return view('clients.pages.documents.index', compact(
            'documents',
            'brands',
            'categories',
            'search',
            'brandSlug',
            'catSlug',
            'fileType',
            'totalDocsCount'
        ));
    }

    /**
     * Tải trực tiếp tài liệu catalog về máy tính (Force File Download).
     */
    public function download($id)
    {
        $media = Media::findOrFail($id);
        
        $rawName = $media->original_name ?: $media->filename;
        $ext = $media->extension ?: pathinfo(parse_url($media->filename, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'pdf';
        
        // Lấy basename sạch sẽ
        $downloadName = basename(parse_url($rawName, PHP_URL_PATH) ?? $rawName);
        if (!str_ends_with(strtolower($downloadName), '.' . strtolower($ext))) {
            $downloadName .= '.' . $ext;
        }

        // 1. Nếu file nằm trên local storage của server
        if ($media->existsOnDisk()) {
            return response()->download($media->absolute_path, $downloadName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
            ]);
        }

        // 2. Nếu file là link external / URL ngoài -> Stream tải trực tiếp về máy người dùng
        $fileUrl = $media->url;
        if (filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            return response()->streamDownload(function () use ($fileUrl) {
                $opts = [
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 60,
                        'follow_location' => 1,
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ];
                $context = stream_context_create($opts);
                $handle = @fopen($fileUrl, 'rb', false, $context);
                if ($handle) {
                    while (!feof($handle)) {
                        echo fread($handle, 1024 * 64);
                        flush();
                    }
                    fclose($handle);
                }
            }, $downloadName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        abort(404, 'File tài liệu không tồn tại');
    }
}
