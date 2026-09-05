<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Series;
use App\Models\Post;
use App\Models\Media;

class SitemapController extends Controller
{
    /**
     * Thư mục chứa sitemap con trong public
     */
    protected string $sitemapDir;

    public function __construct()
    {
        $this->sitemapDir = public_path('sitemaps');
    }

    /**
     * Hiển thị trang quản lý Sitemap trong Admin
     */
    public function index()
    {
        return view('admins.pages.sitemaps.index');
    }

    /**
     * Lấy cài đặt Sitemap & danh sách file sitemap hiện có
     */
    public function apiGetInfo()
    {
        try {
            $settings = $this->getSitemapSettings();
            $files = $this->scanExistingSitemaps();

            $totalLinks = 0;
            foreach ($files as $file) {
                if (!$file['is_index']) {
                    $totalLinks += $file['link_count'];
                }
            }

            return response()->json([
                'success' => true,
                'settings' => $settings,
                'files' => $files,
                'summary' => [
                    'total_files' => count($files),
                    'total_links' => $totalLinks,
                    'last_generated' => $settings['sitemap_last_generated'] ?? null,
                    'index_url' => url('sitemap.xml'),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Sitemap apiGetInfo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin sitemap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu cài đặt cấu hình Sitemap
     */
    public function apiSaveSettings(Request $request)
    {
        try {
            $request->validate([
                'sitemap_max_links_per_file' => 'required|integer|min:100|max:50000',
                'sitemap_include_products' => 'nullable|boolean',
                'sitemap_include_categories' => 'nullable|boolean',
                'sitemap_include_series' => 'nullable|boolean',
                'sitemap_include_blogs' => 'nullable|boolean',
                'sitemap_include_images' => 'nullable|boolean',
                'sitemap_include_documents' => 'nullable|boolean',
                'sitemap_include_pages' => 'nullable|boolean',
            ]);

            $keys = [
                'sitemap_max_links_per_file' => (int) $request->input('sitemap_max_links_per_file', 10000),
                'sitemap_include_products' => $request->boolean('sitemap_include_products') ? 1 : 0,
                'sitemap_include_categories' => $request->boolean('sitemap_include_categories') ? 1 : 0,
                'sitemap_include_series' => $request->boolean('sitemap_include_series') ? 1 : 0,
                'sitemap_include_blogs' => $request->boolean('sitemap_include_blogs') ? 1 : 0,
                'sitemap_include_images' => $request->boolean('sitemap_include_images') ? 1 : 0,
                'sitemap_include_documents' => $request->boolean('sitemap_include_documents') ? 1 : 0,
                'sitemap_include_pages' => $request->boolean('sitemap_include_pages') ? 1 : 0,
            ];

            foreach ($keys as $key => $val) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => (string) $val, 'type' => is_int($val) ? 'integer' : 'boolean']
                );
            }

            \Illuminate\Support\Facades\Cache::forget('global_settings');

            return response()->json([
                'success' => true,
                'message' => 'Lưu cấu hình Sitemap thành công!'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Sitemap apiSaveSettings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu cấu hình: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tiến hành tạo toàn bộ Sitemap XML
     */
    public function apiGenerate(Request $request)
    {
        // Tăng giới hạn thực thi để đảm bảo xử lý mượt mà
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $startTime = microtime(true);

        try {
            // Lấy cài đặt hiện tại
            $settings = $this->getSitemapSettings();
            $maxLinks = max(100, (int) ($settings['sitemap_max_links_per_file'] ?? 10000));

            // Đảm bảo thư mục sitemaps tồn tại
            if (!File::isDirectory($this->sitemapDir)) {
                File::makeDirectory($this->sitemapDir, 0755, true);
            } else {
                // Xóa các file xml cũ trong thư mục sitemaps để làm mới hoàn toàn
                $oldFiles = File::glob($this->sitemapDir . '/*.xml');
                foreach ($oldFiles as $f) {
                    @unlink($f);
                }
            }

            $generatedSubSitemaps = [];
            $totalUrlsCount = 0;

            // 1. TẠO SITEMAP TRANG TĨNH (PAGES)
            if (!empty($settings['sitemap_include_pages'])) {
                $pagesResult = $this->generatePagesSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $pagesResult['files']);
                $totalUrlsCount += $pagesResult['total'];
            }

            // 2. TẠO SITEMAP DANH MỤC (CATEGORIES)
            if (!empty($settings['sitemap_include_categories'])) {
                $catResult = $this->generateCategoriesSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $catResult['files']);
                $totalUrlsCount += $catResult['total'];
            }

            // 3. TẠO SITEMAP DÒNG SẢN PHẨM (SERIES)
            if (!empty($settings['sitemap_include_series'])) {
                $seriesResult = $this->generateSeriesSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $seriesResult['files']);
                $totalUrlsCount += $seriesResult['total'];
            }

            // 4. TẠO SITEMAP SẢN PHẨM (PRODUCTS) - CÓ PHÂN CHIA NHIỀU FILE NẾU VƯỢT MAX
            if (!empty($settings['sitemap_include_products'])) {
                $prodResult = $this->generateProductsSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $prodResult['files']);
                $totalUrlsCount += $prodResult['total'];
            }

            // 5. TẠO SITEMAP BÀI VIẾT (BLOGS/NEWS)
            if (!empty($settings['sitemap_include_blogs'])) {
                $blogResult = $this->generateBlogsSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $blogResult['files']);
                $totalUrlsCount += $blogResult['total'];
            }

            // 6. TẠO SITEMAP TÀI LIỆU (DOCUMENTS)
            if (!empty($settings['sitemap_include_documents'])) {
                $docResult = $this->generateDocumentsSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $docResult['files']);
                $totalUrlsCount += $docResult['total'];
            }

            // 7. TẠO SITEMAP HÌNH ẢNH (IMAGES)
            if (!empty($settings['sitemap_include_images'])) {
                $imgResult = $this->generateImagesSitemap($maxLinks);
                $generatedSubSitemaps = array_merge($generatedSubSitemaps, $imgResult['files']);
                $totalUrlsCount += $imgResult['total'];
            }

            // 8. TẠO FILE CHỈ MỤC TỔNG: public/sitemap.xml (Sitemap Index)
            $this->generateMasterSitemapIndex($generatedSubSitemaps);

            // 9. Cập nhật robots.txt
            $this->ensureRobotsTxtSitemap();

            // 10. Lưu thời gian tạo
            $nowStr = now()->toDateTimeString();
            Setting::updateOrCreate(
                ['key' => 'sitemap_last_generated'],
                ['value' => $nowStr, 'type' => 'string']
            );
            \Illuminate\Support\Facades\Cache::forget('global_settings');

            $duration = round(microtime(true) - $startTime, 2);

            return response()->json([
                'success' => true,
                'message' => "Tạo Sitemap thành công! Đã tạo " . count($generatedSubSitemaps) . " sitemap con với tổng số {$totalUrlsCount} link (Xử lý trong {$duration}s).",
                'files' => $this->scanExistingSitemaps(),
                'summary' => [
                    'total_files' => count($generatedSubSitemaps) + 1,
                    'total_links' => $totalUrlsCount,
                    'last_generated' => $nowStr,
                    'duration' => $duration,
                    'index_url' => url('sitemap.xml'),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Sitemap apiGenerate error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi trong quá trình tạo sitemap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sinh sitemap các trang tĩnh chính của website
     */
    protected function generatePagesSitemap(int $maxLinks): array
    {
        $staticRoutes = [
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('shop.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('brands.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('documents.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('blogs.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('contact.index'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('quote.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        $items = [];
        $nowAtom = now()->toAtomString();
        foreach ($staticRoutes as $r) {
            $items[] = [
                'loc' => $r['loc'],
                'lastmod' => $nowAtom,
                'changefreq' => $r['changefreq'],
                'priority' => $r['priority'],
            ];
        }

        return $this->writeChunkedSitemaps('pages', $items, $maxLinks);
    }

    /**
     * Sinh sitemap danh mục sản phẩm (Categories)
     */
    protected function generateCategoriesSitemap(int $maxLinks): array
    {
        $categories = Category::query()
            ->where('status', 'active')
            ->where('type', 'product')
            ->select(['id', 'slug', 'updated_at', 'created_at'])
            ->orderBy('id', 'asc')
            ->get();

        $items = [];
        foreach ($categories as $cat) {
            $lastmod = ($cat->updated_at ?: $cat->created_at ?: now())->toAtomString();
            $items[] = [
                'loc' => route('categories.show', $cat->slug),
                'lastmod' => $lastmod,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        }

        return $this->writeChunkedSitemaps('categories', $items, $maxLinks);
    }

    /**
     * Sinh sitemap dòng sản phẩm (Series)
     */
    protected function generateSeriesSitemap(int $maxLinks): array
    {
        $seriesList = Series::query()
            ->where('status', 'active')
            ->select(['id', 'slug', 'updated_at', 'created_at'])
            ->orderBy('id', 'asc')
            ->get();

        $items = [];
        foreach ($seriesList as $ser) {
            $lastmod = ($ser->updated_at ?: $ser->created_at ?: now())->toAtomString();
            $items[] = [
                'loc' => route('series.show', $ser->slug),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        return $this->writeChunkedSitemaps('series', $items, $maxLinks);
    }

    /**
     * Sinh sitemap Sản phẩm (Products) - Chỉ lấy sản phẩm ĐÃ XUẤT BẢN (published_at <= now)
     */
    protected function generateProductsSitemap(int $maxLinks): array
    {
        $query = Product::query()
            ->published()
            ->select(['id', 'slug', 'updated_at', 'created_at', 'published_at'])
            ->orderBy('id', 'asc');

        $total = $query->count();
        if ($total === 0) {
            return ['files' => [], 'total' => 0];
        }

        $files = [];
        $fileIndex = 0;
        $currentItems = [];
        $processedCount = 0;

        $query->chunk(1000, function ($products) use (&$files, &$fileIndex, &$currentItems, &$processedCount, $maxLinks) {
            foreach ($products as $p) {
                $lastmod = ($p->updated_at ?: $p->published_at ?: $p->created_at ?: now())->toAtomString();
                $currentItems[] = [
                    'loc' => route('product.show', $p->slug),
                    'lastmod' => $lastmod,
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ];
                $processedCount++;

                if (count($currentItems) >= $maxLinks) {
                    $filename = $this->buildSitemapFilename('products', $fileIndex);
                    $this->writeStandardXmlFile($filename, $currentItems);
                    $files[] = $filename;
                    $fileIndex++;
                    $currentItems = [];
                }
            }
        });

        if (!empty($currentItems)) {
            $filename = $this->buildSitemapFilename('products', $fileIndex);
            $this->writeStandardXmlFile($filename, $currentItems);
            $files[] = $filename;
        }

        return ['files' => $files, 'total' => $processedCount];
    }

    /**
     * Sinh sitemap Bài viết (Blogs/News) - Chỉ lấy bài viết đã xuất bản
     */
    protected function generateBlogsSitemap(int $maxLinks): array
    {
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $posts = Post::query()
            ->whereIn('status', ['published', 'active'])
            ->where(function ($q) use ($now) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', $now);
            })
            ->select(['id', 'slug', 'updated_at', 'created_at', 'published_at'])
            ->orderBy('id', 'asc')
            ->get();

        $items = [];
        foreach ($posts as $post) {
            $lastmod = ($post->updated_at ?: $post->published_at ?: $post->created_at ?: now())->toAtomString();
            $items[] = [
                'loc' => route('blogs.show', $post->slug),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        return $this->writeChunkedSitemaps('blogs', $items, $maxLinks);
    }

    /**
     * Sinh sitemap Tài liệu (Documents) - Chỉ gắn với sản phẩm đã xuất bản
     */
    protected function generateDocumentsSitemap(int $maxLinks): array
    {
        $docs = Media::query()
            ->whereHas('products', function ($pQuery) {
                $pQuery->where('product_media.role', 'catalog')
                       ->published();
            })
            ->select(['id', 'filename', 'updated_at', 'created_at'])
            ->orderBy('id', 'asc')
            ->get();

        $items = [];
        foreach ($docs as $doc) {
            $lastmod = ($doc->updated_at ?: $doc->created_at ?: now())->toAtomString();
            $items[] = [
                'loc' => route('documents.download', $doc->id),
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        return $this->writeChunkedSitemaps('documents', $items, $maxLinks);
    }

    /**
     * Sinh sitemap Hình ảnh sản phẩm & bài viết (Google Image Sitemap) - Chỉ lấy từ sản phẩm đã xuất bản
     */
    protected function generateImagesSitemap(int $maxLinks): array
    {
        $products = Product::query()
            ->published()
            ->with(['thumbnailMedia', 'galleryMedia'])
            ->select(['id', 'name', 'slug', 'updated_at'])
            ->orderBy('id', 'asc')
            ->get();

        $imageEntries = [];
        foreach ($products as $p) {
            $pageUrl = route('product.show', $p->slug);
            $imgs = [];

            if ($p->thumbnailMedia && !empty($p->thumbnailMedia->url)) {
                $imgs[] = [
                    'loc' => $p->thumbnailMedia->url,
                    'title' => $p->name,
                ];
            }

            if ($p->galleryMedia) {
                foreach ($p->galleryMedia as $gImg) {
                    if (!empty($gImg->url) && count($imgs) < 10) {
                        $imgs[] = [
                            'loc' => $gImg->url,
                            'title' => $p->name,
                        ];
                    }
                }
            }

            if (!empty($imgs)) {
                $imageEntries[] = [
                    'loc' => $pageUrl,
                    'images' => $imgs,
                ];
            }
        }

        if (empty($imageEntries)) {
            return ['files' => [], 'total' => 0];
        }

        $chunks = array_chunk($imageEntries, $maxLinks);
        $files = [];
        $total = count($imageEntries);

        foreach ($chunks as $index => $chunk) {
            $filename = $this->buildSitemapFilename('images', $index);
            $this->writeImageXmlFile($filename, $chunk);
            $files[] = $filename;
        }

        return ['files' => $files, 'total' => $total];
    }

    /**
     * Tự động chia file sitemap khi số lượng link vượt quá max_links_per_file
     * Quy tắc tên file:
     * - File 1: {prefix}-sitemap.xml
     * - File 2: {prefix}-1-sitemap.xml
     * - File 3: {prefix}-2-sitemap.xml
     */
    protected function writeChunkedSitemaps(string $prefix, array $items, int $maxLinks): array
    {
        if (empty($items)) {
            return ['files' => [], 'total' => 0];
        }

        $chunks = array_chunk($items, $maxLinks);
        $files = [];
        $total = count($items);

        foreach ($chunks as $index => $chunk) {
            $filename = $this->buildSitemapFilename($prefix, $index);
            $this->writeStandardXmlFile($filename, $chunk);
            $files[] = $filename;
        }

        return ['files' => $files, 'total' => $total];
    }

    /**
     * Tạo tên file chuẩn theo quy ước người dùng:
     * Index 0 -> prefix-sitemap.xml
     * Index 1 -> prefix-1-sitemap.xml
     * Index 2 -> prefix-2-sitemap.xml
     */
    protected function buildSitemapFilename(string $prefix, int $index): string
    {
        if ($index === 0) {
            return "{$prefix}-sitemap.xml";
        }
        return "{$prefix}-{$index}-sitemap.xml";
    }

    /**
     * Ghi file XML chuẩn URL thông thường
     */
    protected function writeStandardXmlFile(string $filename, array $items): void
    {
        $filePath = $this->sitemapDir . DIRECTORY_SEPARATOR . $filename;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($items as $item) {
            $loc = htmlspecialchars($item['loc'], ENT_QUOTES, 'UTF-8');
            $lastmod = htmlspecialchars($item['lastmod'] ?? now()->toAtomString(), ENT_QUOTES, 'UTF-8');
            $changefreq = htmlspecialchars($item['changefreq'] ?? 'weekly', ENT_QUOTES, 'UTF-8');
            $priority = htmlspecialchars($item['priority'] ?? '0.8', ENT_QUOTES, 'UTF-8');

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        File::put($filePath, $xml);
    }

    /**
     * Ghi file XML chuẩn Google Image Sitemap
     */
    protected function writeImageXmlFile(string $filename, array $entries): void
    {
        $filePath = $this->sitemapDir . DIRECTORY_SEPARATOR . $filename;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($entries as $entry) {
            $loc = htmlspecialchars($entry['loc'], ENT_QUOTES, 'UTF-8');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            foreach ($entry['images'] as $img) {
                $imgLoc = htmlspecialchars($img['loc'], ENT_QUOTES, 'UTF-8');
                $imgTitle = htmlspecialchars($img['title'] ?? '', ENT_QUOTES, 'UTF-8');
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>{$imgLoc}</image:loc>\n";
                if (!empty($imgTitle)) {
                    $xml .= "      <image:title>{$imgTitle}</image:title>\n";
                }
                $xml .= "    </image:image>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        File::put($filePath, $xml);
    }

    /**
     * Ghi file tổng Sitemap Index: public/sitemap.xml
     */
    protected function generateMasterSitemapIndex(array $subSitemaps): void
    {
        $indexPath = public_path('sitemap.xml');
        $nowAtom = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($subSitemaps as $filename) {
            $loc = htmlspecialchars(url('sitemaps/' . $filename), ENT_QUOTES, 'UTF-8');
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <lastmod>{$nowAtom}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';
        File::put($indexPath, $xml);
    }

    /**
     * Đảm bảo file robots.txt có dòng Sitemap
     */
    protected function ensureRobotsTxtSitemap(): void
    {
        $robotsPath = public_path('robots.txt');
        $sitemapUrl = url('sitemap.xml');
        $sitemapLine = "Sitemap: {$sitemapUrl}";

        if (File::exists($robotsPath)) {
            $content = File::get($robotsPath);
            if (stripos($content, 'Sitemap:') === false) {
                $content = trim($content) . "\n\n" . $sitemapLine . "\n";
                File::put($robotsPath, $content);
            } else {
                // Thay thế dòng Sitemap cũ bằng URL mới chính xác
                $content = preg_replace('/^Sitemap:\s*.*$/mi', $sitemapLine, $content);
                File::put($robotsPath, $content);
            }
        } else {
            File::put($robotsPath, "User-agent: *\nDisallow:\n\n{$sitemapLine}\n");
        }
    }

    /**
     * Lấy cài đặt sitemap từ bảng Settings
     */
    protected function getSitemapSettings(): array
    {
        $defaults = [
            'sitemap_max_links_per_file' => 10000,
            'sitemap_include_products' => 1,
            'sitemap_include_categories' => 1,
            'sitemap_include_series' => 1,
            'sitemap_include_blogs' => 1,
            'sitemap_include_images' => 1,
            'sitemap_include_documents' => 1,
            'sitemap_include_pages' => 1,
            'sitemap_last_generated' => null,
        ];

        $keys = array_keys($defaults);
        $dbSettings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $result = [];
        foreach ($defaults as $k => $def) {
            if (isset($dbSettings[$k])) {
                $val = $dbSettings[$k];
                if (is_numeric($def) && is_numeric($val)) {
                    $result[$k] = (int) $val;
                } else {
                    $result[$k] = $val;
                }
            } else {
                $result[$k] = $def;
            }
        }

        return $result;
    }

    /**
     * Quét các file sitemap XML đang tồn tại trên đĩa
     */
    protected function scanExistingSitemaps(): array
    {
        $list = [];

        // 1. File tổng public/sitemap.xml
        $masterPath = public_path('sitemap.xml');
        if (File::exists($masterPath)) {
            $size = File::size($masterPath);
            $mtime = File::lastModified($masterPath);
            $content = File::get($masterPath);
            $count = substr_count($content, '<sitemap>');

            $list[] = [
                'name' => 'sitemap.xml',
                'rel_path' => 'sitemap.xml',
                'url' => url('sitemap.xml'),
                'is_index' => true,
                'type_label' => 'Sitemap Index (Chính)',
                'link_count' => $count,
                'size_formatted' => $this->formatFileSize($size),
                'updated_at' => date('d/m/Y H:i:s', $mtime),
            ];
        }

        // 2. Các file con trong public/sitemaps/
        if (File::isDirectory($this->sitemapDir)) {
            $files = File::glob($this->sitemapDir . '/*.xml');
            foreach ($files as $filePath) {
                $fileName = basename($filePath);
                $size = File::size($filePath);
                $mtime = File::lastModified($filePath);
                $content = File::get($filePath);
                $count = substr_count($content, '<url>');

                $typeLabel = $this->determineSitemapTypeLabel($fileName);

                $list[] = [
                    'name' => $fileName,
                    'rel_path' => 'sitemaps/' . $fileName,
                    'url' => url('sitemaps/' . $fileName),
                    'is_index' => false,
                    'type_label' => $typeLabel,
                    'link_count' => $count,
                    'size_formatted' => $this->formatFileSize($size),
                    'updated_at' => date('d/m/Y H:i:s', $mtime),
                ];
            }
        }

        return $list;
    }

    /**
     * Xác định nhãn hiển thị loại sitemap
     */
    protected function determineSitemapTypeLabel(string $fileName): string
    {
        if (str_starts_with($fileName, 'products')) {
            return 'Sản phẩm (Products)';
        }
        if (str_starts_with($fileName, 'categories')) {
            return 'Danh mục (Categories)';
        }
        if (str_starts_with($fileName, 'series')) {
            return 'Dòng SP (Series)';
        }
        if (str_starts_with($fileName, 'blogs')) {
            return 'Bài viết (Blogs/News)';
        }
        if (str_starts_with($fileName, 'images')) {
            return 'Hình ảnh (Google Images)';
        }
        if (str_starts_with($fileName, 'documents')) {
            return 'Tài liệu (Documents)';
        }
        if (str_starts_with($fileName, 'pages')) {
            return 'Trang tĩnh (Pages)';
        }
        return 'Khác (XML)';
    }

    /**
     * Định dạng dung lượng file
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
