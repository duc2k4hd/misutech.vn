<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Series;
use App\Models\Media;
use App\Models\Review;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLast  = $now->copy()->subMonth()->startOfMonth();
        $endOfLast    = $now->copy()->subMonth()->endOfMonth();

        // â”€â”€ Tá»•ng quan há»‡ thá»‘ng â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $totalUsers           = User::count();
        $totalProducts        = Product::count();
        $totalProductsActive  = Product::where('status', 'active')->count();
        $totalOrders          = Order::count();
        $totalRevenue         = Order::where('status', 'completed')->sum('total_amount');

        $totalQuotes          = Quote::count();
        $totalQuotesNew       = Quote::where('status', 'new')->count();
        $totalQuotesPending   = Quote::whereIn('status', ['new', 'processing'])->count();
        $totalQuoteRevenue    = Quote::where('status', '!=', 'cancelled')->sum('total_amount');

        $totalContacts        = Contact::count();
        $totalContactsNew     = Contact::where('status', 'new')->count();

        $totalPosts           = Post::count();
        $totalPostsPublished  = Post::where('status', 'published')->count();

        $totalCategories      = Category::count();
        $totalBrands          = Brand::count();
        $totalSeries          = Series::count();
        $totalMediaFiles      = Media::count();
        $totalMediaSize       = Media::sum('size');

        $totalReviews         = Review::count();
        $totalReviewsNew      = Review::where('status', 'pending')->count();
        $avgRating            = round(Review::where('status', 'approved')->avg('rating') ?? 0, 1);

        // â”€â”€ ThÃ¡ng nÃ y vs thÃ¡ng trÆ°á»›c â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $quotesThisMonth  = Quote::where('created_at', '>=', $startOfMonth)->count();
        $quotesLastMonth  = Quote::whereBetween('created_at', [$startOfLast, $endOfLast])->count();
        $quoteGrowth      = $quotesLastMonth > 0
            ? round((($quotesThisMonth - $quotesLastMonth) / $quotesLastMonth) * 100, 1)
            : ($quotesThisMonth > 0 ? 100 : 0);

        $contactsThisMonth = Contact::where('created_at', '>=', $startOfMonth)->count();
        $contactsLastMonth = Contact::whereBetween('created_at', [$startOfLast, $endOfLast])->count();
        $contactGrowth    = $contactsLastMonth > 0
            ? round((($contactsThisMonth - $contactsLastMonth) / $contactsLastMonth) * 100, 1)
            : ($contactsThisMonth > 0 ? 100 : 0);

        // â”€â”€ Biá»ƒu Ä‘á»“ bÃ¡o giÃ¡ 12 thÃ¡ng gáº§n nháº¥t â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $chartMonths  = [];
        $chartQuotes  = [];
        $chartRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $m     = $now->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            $chartMonths[]  = $m->format('m/Y');
            $chartQuotes[]  = Quote::whereBetween('created_at', [$start, $end])->count();
            $chartRevenue[] = (float) Quote::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');
        }

        // â”€â”€ Top 8 sáº£n pháº©m Ä‘Æ°á»£c bÃ¡o giÃ¡ nhiá»u nháº¥t â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $topQuotedProducts = QuoteItem::select(
                'product_name', 'product_sku', 'brand_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(total_price) as total_value'),
                DB::raw('COUNT(*) as quote_count')
            )
            ->whereNotNull('product_name')
            ->groupBy('product_name', 'product_sku', 'brand_name')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        // â”€â”€ BÃ¡o giÃ¡ gáº§n nháº¥t (10 cÃ¡i) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $recentQuotes = Quote::select(
                'id', 'quote_code', 'customer_name', 'customer_phone',
                'customer_company', 'total_amount', 'items_count', 'status', 'created_at'
            )
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // â”€â”€ LiÃªn há»‡ gáº§n nháº¥t (8 cÃ¡i) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $recentContacts = Contact::select('id', 'name', 'email', 'phone', 'subject', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // â”€â”€ BÃ i viáº¿t gáº§n nháº¥t â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $recentPosts = Post::select('id', 'title', 'slug', 'status', 'views_count', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // â”€â”€ Sáº£n pháº©m má»›i nháº¥t â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $recentProducts = Product::select('id', 'name', 'sku', 'price', 'status', 'created_at')
            ->with(['brand:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // â”€â”€ Review gáº§n nháº¥t â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $recentReviews = Review::select('id', 'author_name', 'rating', 'comment', 'status', 'product_id', 'created_at')
            ->with(['product:id,name,sku'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // â”€â”€ Thá»‘ng kÃª bÃ¡o giÃ¡ theo tráº¡ng thÃ¡i â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $quoteByStatus = Quote::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // â”€â”€ Thá»‘ng kÃª liÃªn há»‡ theo thiáº¿t bá»‹ â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $contactByDevice = Contact::select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        // â”€â”€ Dung lÆ°á»£ng Media dáº¡ng Ä‘á»c Ä‘Æ°á»£c â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $mediaSizeHuman = $this->formatBytes((int) $totalMediaSize);

        return view('admins.pages.dashboard.index', compact(
            'totalUsers', 'totalProducts', 'totalProductsActive',
            'totalOrders', 'totalRevenue',
            'totalQuotes', 'totalQuotesNew', 'totalQuotesPending', 'totalQuoteRevenue',
            'totalContacts', 'totalContactsNew',
            'totalPosts', 'totalPostsPublished',
            'totalCategories', 'totalBrands', 'totalSeries',
            'totalMediaFiles', 'mediaSizeHuman',
            'totalReviews', 'totalReviewsNew', 'avgRating',
            'quotesThisMonth', 'quotesLastMonth', 'quoteGrowth',
            'contactsThisMonth', 'contactsLastMonth', 'contactGrowth',
            'chartMonths', 'chartQuotes', 'chartRevenue',
            'topQuotedProducts',
            'recentQuotes', 'recentContacts',
            'recentPosts', 'recentProducts', 'recentReviews',
            'quoteByStatus', 'contactByDevice'
        ));
    }

    /**
     * Format bytes to human-readable string.
     */
    private function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes < 1024)          return $bytes . ' B';
        if ($bytes < 1_048_576)     return round($bytes / 1024, $precision) . ' KB';
        if ($bytes < 1_073_741_824) return round($bytes / 1_048_576, $precision) . ' MB';
        return round($bytes / 1_073_741_824, $precision) . ' GB';
    }

    /**
     * API TÃ¬m kiáº¿m toÃ n nÄƒng (Global Live Search) cho Admin
     */
    public function globalSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (empty($q) || mb_strlen($q) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $results = [];

        // 1. PhÃ­m táº¯t / Äiá»u hÆ°á»›ng há»‡ thá»‘ng
        $shortcuts = [
            ['title' => 'Quáº£n lÃ½ BÃ¡o GiÃ¡', 'desc' => 'Xem danh sÃ¡ch vÃ  xá»­ lÃ½ bÃ¡o giÃ¡ khÃ¡ch hÃ ng', 'icon' => 'mdi mdi-file-document-box', 'url' => route('admin.quotes.index'), 'keywords' => 'bao gia quote gia don hang'],
            ['title' => 'KhÃ¡ch HÃ ng LiÃªn Há»‡', 'desc' => 'Xem danh sÃ¡ch pháº£n há»“i & tin nháº¯n liÃªn há»‡', 'icon' => 'mdi mdi-email-open', 'url' => route('admin.contacts.index'), 'keywords' => 'lien he contact feedback tin nhan'],
            ['title' => 'Hotline & TÆ° Váº¥n (Sale/Báº£o hÃ nh)', 'desc' => 'Quáº£n lÃ½ danh báº¡ hotline há»— trá»£ hiá»ƒn thá»‹ trang chá»§', 'icon' => 'mdi mdi-phone-classic', 'url' => route('admin.support_contacts.index'), 'keywords' => 'hotline sale bao hanh tu van so dien thoai'],
            ['title' => 'Quáº£n lÃ½ Sáº£n pháº©m', 'desc' => 'ThÃªm má»›i, sá»­a giÃ¡, cáº­p nháº­t tá»“n kho sáº£n pháº©m', 'icon' => 'mdi mdi-store', 'url' => route('admin.products.index'), 'keywords' => 'san pham product thiet bi kho'],
            ['title' => 'Quáº£n lÃ½ Danh má»¥c', 'desc' => 'Cáº¥u hÃ¬nh cÃ¢y danh má»¥c thiáº¿t bá»‹ tá»± Ä‘á»™ng hÃ³a', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => route('admin.categories.index'), 'keywords' => 'danh muc category'],
            ['title' => 'DÃ²ng sáº£n pháº©m (Series)', 'desc' => 'Quáº£n lÃ½ cÃ¡c dÃ²ng sáº£n pháº©m cá»§a thÆ°Æ¡ng hiá»‡u', 'icon' => 'mdi mdi-layers', 'url' => route('admin.series.index'), 'keywords' => 'dong san pham series'],
            ['title' => 'Quáº£n lÃ½ BÃ i viáº¿t & Tin tá»©c', 'desc' => 'Viáº¿t bÃ i chia sáº» ká»¹ thuáº­t, tin tá»©c tá»± Ä‘á»™ng hÃ³a', 'icon' => 'mdi mdi-newspaper', 'url' => route('admin.posts.index'), 'keywords' => 'bai viet post tin tuc blog'],
            ['title' => 'Quáº£n lÃ½ Banner Slider', 'desc' => 'Cáº­p nháº­t banner quáº£ng cÃ¡o trang chá»§', 'icon' => 'mdi mdi-image-multiple', 'url' => route('admin.banners.index'), 'keywords' => 'banner quang cao slider hinh anh'],
            ['title' => 'ThÆ° viá»‡n Media & Tá»‡p', 'desc' => 'Táº£i lÃªn & quáº£n lÃ½ tÃ i liá»‡u catalogue, hÃ¬nh áº£nh', 'icon' => 'mdi mdi-folder-multiple-image', 'url' => route('admin.media.index'), 'keywords' => 'media file tep tin thu vien'],
            ['title' => 'CÃ i Ä‘áº·t Há»‡ Thá»‘ng', 'desc' => 'Cáº¥u hÃ¬nh Logo, Hotline, Email, SEO, Máº¡ng xÃ£ há»™i', 'icon' => 'mdi mdi-settings', 'url' => route('admin.settings.index'), 'keywords' => 'cai dat setting cau hinh he thong logo seo'],
        ];

        $matchedShortcuts = [];
        $lowerQ = mb_strtolower($q);
        foreach ($shortcuts as $sc) {
            if (str_contains(mb_strtolower($sc['title']), $lowerQ) || str_contains($sc['keywords'], $lowerQ)) {
                $matchedShortcuts[] = [
                    'group'      => 'Lá»‘i táº¯t chá»©c nÄƒng',
                    'title'      => $sc['title'],
                    'desc'       => $sc['desc'],
                    'icon'       => $sc['icon'],
                    'badge'      => 'Chá»©c nÄƒng',
                    'badge_class'=> 'badge-info',
                    'url'        => $sc['url'],
                ];
            }
        }
        if (!empty($matchedShortcuts)) {
            $results = array_merge($results, array_slice($matchedShortcuts, 0, 3));
        }

        // 2. TÃ¬m trong Sáº£n pháº©m (Products)
        try {
            $products = Product::where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
                ->take(5)->get();

            foreach ($products as $p) {
                $results[] = [
                    'group'      => 'Sáº£n pháº©m',
                    'title'      => $p->name,
                    'desc'       => ($p->sku ? "MÃ£ SKU: {$p->sku} â€¢ " : "") . number_format($p->price ?? 0, 0, ',', '.') . ' Ä‘',
                    'icon'       => 'mdi mdi-cube-outline',
                    'badge'      => 'Sáº£n pháº©m',
                    'badge_class'=> 'badge-primary',
                    'url'        => route('admin.products.index') . '?search=' . urlencode($p->sku ?: $p->name),
                ];
            }
        } catch (\Exception $e) {}

        // 3. TÃ¬m trong BÃ¡o giÃ¡ (Quotes)
        try {
            $quotes = Quote::where('quote_code', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->orWhere('customer_phone', 'like', "%{$q}%")
                ->orWhere('customer_email', 'like', "%{$q}%")
                ->take(4)->get();

            foreach ($quotes as $quote) {
                $results[] = [
                    'group'      => 'BÃ¡o giÃ¡',
                    'title'      => '#' . $quote->quote_code . ' - ' . ($quote->customer_name ?: 'KhÃ¡ch hÃ ng'),
                    'desc'       => 'SÄT: ' . ($quote->customer_phone ?: 'N/A') . ' â€¢ ' . number_format($quote->total_amount ?? 0, 0, ',', '.') . ' Ä‘',
                    'icon'       => 'mdi mdi-file-document-box',
                    'badge'      => 'BÃ¡o giÃ¡',
                    'badge_class'=> 'badge-warning',
                    'url'        => route('admin.quotes.index') . '?search=' . urlencode($quote->quote_code),
                ];
            }
        } catch (\Exception $e) {}

        // 4. TÃ¬m trong KhÃ¡ch HÃ ng LiÃªn Há»‡ (Contacts)
        try {
            $contacts = Contact::where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('subject', 'like', "%{$q}%")
                ->take(4)->get();

            foreach ($contacts as $c) {
                $results[] = [
                    'group'      => 'KhÃ¡ch hÃ ng liÃªn há»‡',
                    'title'      => $c->name . ' (' . ($c->phone ?: $c->email) . ')',
                    'desc'       => Str::limit($c->subject ?: ($c->message ?: 'Ná»™i dung liÃªn há»‡'), 50),
                    'icon'       => 'mdi mdi-email-open',
                    'badge'      => 'LiÃªn há»‡',
                    'badge_class'=> 'badge-success',
                    'url'        => route('admin.contacts.index') . '?search=' . urlencode($c->phone ?: $c->name),
                ];
            }
        } catch (\Exception $e) {}

        // 5. TÃ¬m trong BÃ i viáº¿t (Posts)
        try {
            $posts = Post::where('title', 'like', "%{$q}%")->take(3)->get();
            foreach ($posts as $post) {
                $results[] = [
                    'group'      => 'BÃ i viáº¿t',
                    'title'      => $post->title,
                    'desc'       => 'BÃ i viáº¿t tin tá»©c & ká»¹ thuáº­t',
                    'icon'       => 'mdi mdi-newspaper',
                    'badge'      => 'BÃ i viáº¿t',
                    'badge_class'=> 'badge-secondary',
                    'url'        => route('admin.posts.index') . '?search=' . urlencode($post->title),
                ];
            }
        } catch (\Exception $e) {}

        // 6. TÃ¬m trong Danh má»¥c & ThÆ°Æ¡ng hiá»‡u
        try {
            $categories = Category::where('name', 'like', "%{$q}%")->take(2)->get();
            foreach ($categories as $cat) {
                $results[] = [
                    'group'      => 'Danh má»¥c',
                    'title'      => 'Danh má»¥c: ' . $cat->name,
                    'desc'       => 'Quáº£n lÃ½ cÃ¢y danh má»¥c sáº£n pháº©m',
                    'icon'       => 'mdi mdi-format-list-bulleted',
                    'badge'      => 'Danh má»¥c',
                    'badge_class'=> 'badge-dark',
                    'url'        => route('admin.categories.index'),
                ];
            }

            $brands = Brand::where('name', 'like', "%{$q}%")->take(2)->get();
            foreach ($brands as $b) {
                $results[] = [
                    'group'      => 'ThÆ°Æ¡ng hiá»‡u',
                    'title'      => 'HÃ£ng: ' . $b->name,
                    'desc'       => 'Quáº£n lÃ½ thÆ°Æ¡ng hiá»‡u thiáº¿t bá»‹',
                    'icon'       => 'mdi mdi-tag-text-outline',
                    'badge'      => 'ThÆ°Æ¡ng hiá»‡u',
                    'badge_class'=> 'badge-info',
                    'url'        => route('admin.brands.index'),
                ];
            }
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'count'   => count($results),
            'results' => $results,
        ]);
    }
}
