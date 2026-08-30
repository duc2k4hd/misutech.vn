<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    /**
     * Hiển thị trang Công cụ Tự Lập Báo Giá Trực Tuyến & Xuất PDF.
     */
    public function index(Request $request)
    {
        // Lấy danh sách sản phẩm gợi ý ban đầu (Top bán chạy / nổi bật - Cached)
        $popularProductsData = \Illuminate\Support\Facades\Cache::remember('quote_popular_products', 1800, function () {
            return Product::published()
                ->with(['brand:id,name', 'category:id,name', 'thumbnailMedia'])
                ->select('id', 'name', 'slug', 'sku', 'price', 'sale_price', 'brand_id', 'category_id')
                ->orderBy('id', 'desc')
                ->limit(12)
                ->get();
        });
        $popularProducts = $popularProductsData;

        return view('clients.pages.quote.index', compact('popularProducts'));
    }

    /**
     * API Tìm kiếm sản phẩm siêu tốc cho công cụ Báo giá (Hỗ trợ hàng triệu sản phẩm).
     */
    public function searchProducts(Request $request)
    {
        $ip = $request->ip();
        $rateLimitKey = 'quote-search:' . $ip;

        // Giới hạn 60 lượt tìm kiếm / phút / IP để chống scan dữ liệu / DoS
        if (RateLimiter::tooManyAttempts($rateLimitKey, 60)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh, vui lòng thử lại sau vài giây.'
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $keyword = trim($request->input('q', ''));
        if (mb_strlen($keyword) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // Làm sạch chuỗi tìm kiếm chống XSS/Injection
        $keyword = strip_tags($keyword);
        $keyword = Str::limit($keyword, 100, '');

        $query = Product::published()
            ->with(['brand:id,name', 'category:id,name', 'thumbnailMedia'])
            ->select('id', 'name', 'slug', 'sku', 'price', 'sale_price', 'brand_id', 'category_id');

        // Tìm kiếm kết hợp: Ưu tiên SKU trước -> Tên sản phẩm -> Thương hiệu
        $query->where(function ($q) use ($keyword) {
            $q->where('sku', 'like', "{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%")
              ->orWhere('name', 'like', "%{$keyword}%")
              ->orWhereHas('brand', function ($b) use ($keyword) {
                  $b->where('name', 'like', "%{$keyword}%");
              });
        });

        // Sắp xếp độ khớp chính xác
        $query->orderByRaw("
            CASE 
                WHEN sku = ? THEN 1
                WHEN sku LIKE ? THEN 2
                WHEN name LIKE ? THEN 3
                ELSE 4
            END ASC
        ", [$keyword, "{$keyword}%", "{$keyword}%"]);

        $products = $query->limit(20)->get();

        $formatted = $products->map(function ($p) {
            $thumb = $p->thumbnailMedia->first();
            $thumbUrl = $thumb 
                ? (Str::startsWith($thumb->url, ['http://', 'https://']) ? $thumb->url : asset('storage/clients/imgs/products/' . $thumb->url)) 
                : asset('storage/clients/imgs/products/no-image.png');

            $unitPrice = $p->sale_price ?: $p->price ?: 0;

            return [
                'id'         => $p->id,
                'name'       => $p->name,
                'sku'        => $p->sku ?: 'Đang cập nhật',
                'slug'       => $p->slug,
                'brand'      => $p->brand ? $p->brand->name : 'MISUTECH',
                'category'   => $p->category ? $p->category->name : 'Thiết bị tự động hóa',
                'price'      => (float) $unitPrice,
                'price_text' => $unitPrice > 0 ? number_format($unitPrice, 0, ',', '.') . ' ₫' : 'Liên hệ báo giá',
                'image'      => $thumbUrl,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted
        ]);
    }

    /**
     * API Lưu & Phân tích hành vi khi khách hàng Xuất PDF / In báo giá.
     */
    public function saveAndTrack(Request $request)
    {
        $ip = $request->ip();
        $rateLimitKey = 'quote-save:' . $ip;

        // Giới hạn 10 lượt tạo báo giá / 5 phút trên 1 IP
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã tạo nhiều báo giá trong thời gian ngắn. Vui lòng chờ ít phút.'
            ], 429);
        }

        // Honeypot Check
        if (!empty($request->input('company_hp_val'))) {
            RateLimiter::hit($rateLimitKey, 300);
            return response()->json(['success' => true, 'quote_code' => 'BG-' . date('Ymd') . '-OK']);
        }

        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'max:100'],
            'customer_phone'   => ['required', 'string', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/'],
            'customer_email'   => ['nullable', 'email', 'max:100'],
            'customer_company' => ['nullable', 'string', 'max:150'],
            'customer_tax_code'=> ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string', 'max:255'],
            'subtotal'         => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_percent'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'total_amount'     => ['required', 'numeric', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'action_type'      => ['nullable', 'in:generated_pdf,printed,saved_online'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'items'            => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_sku'  => ['nullable', 'string', 'max:100'],
            'items.*.brand_name'   => ['nullable', 'string', 'max:100'],
            'items.*.unit'         => ['nullable', 'string', 'max:30'],
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:10000'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0'],
            'items.*.total_price'  => ['required', 'numeric', 'min:0'],
        ], [
            'customer_name.required'  => 'Vui lòng nhập họ tên hoặc tên doanh nghiệp của bạn.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại hoặc Zalo để lưu báo giá.',
            'customer_phone.regex'    => 'Số điện thoại không đúng định dạng.',
            'items.required'          => 'Báo giá phải có ít nhất 1 sản phẩm.',
            'items.min'               => 'Báo giá phải có ít nhất 1 sản phẩm.',
            'items.max'               => 'Mỗi bản báo giá tối đa 50 sản phẩm.',
        ]);

        DB::beginTransaction();
        try {
            // Sinh mã báo giá độc nhất (Ví dụ: BG-20260829-K8R2)
            $quoteCode = 'BG-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            while (Quote::where('quote_code', $quoteCode)->exists()) {
                $quoteCode = 'BG-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            $telemetry = \App\Services\TelemetryHelper::extract($request);
            if (isset($validated['duration_seconds']) && $validated['duration_seconds'] > 0) {
                $telemetry['duration_seconds'] = (int) $validated['duration_seconds'];
            }

            $quote = Quote::create([
                'quote_code'        => $quoteCode,
                'customer_name'     => strip_tags(trim($validated['customer_name'])),
                'customer_phone'    => strip_tags(trim($validated['customer_phone'])),
                'customer_email'    => !empty($validated['customer_email']) ? strip_tags(trim($validated['customer_email'])) : null,
                'customer_company'  => !empty($validated['customer_company']) ? strip_tags(trim($validated['customer_company'])) : null,
                'customer_tax_code' => !empty($validated['customer_tax_code']) ? strip_tags(trim($validated['customer_tax_code'])) : null,
                'customer_address'  => !empty($validated['customer_address']) ? strip_tags(trim($validated['customer_address'])) : null,
                'subtotal'          => $validated['subtotal'],
                'discount_percent'  => $validated['discount_percent'] ?? 0,
                'vat_percent'       => $validated['vat_percent'] ?? 10,
                'total_amount'      => $validated['total_amount'],
                'items_count'       => count($validated['items']),
                'notes'             => !empty($validated['notes']) ? strip_tags(trim($validated['notes'])) : null,
                'ip_address'        => $telemetry['ip_address'],
                'user_agent'        => $telemetry['user_agent'],
                'referer'           => $telemetry['referer'],
                'device_type'       => $telemetry['device_type'],
                'duration_seconds'  => $telemetry['duration_seconds'],
                'meta_data'         => $telemetry['meta_data'],
                'action_type'       => $validated['action_type'] ?? 'generated_pdf',
                'status'            => 'submitted',
            ]);

            foreach ($validated['items'] as $item) {
                QuoteItem::create([
                    'quote_id'     => $quote->id,
                    'product_id'   => isset($item['product_id']) && is_numeric($item['product_id']) ? (int) $item['product_id'] : null,
                    'product_name' => strip_tags(trim($item['product_name'])),
                    'product_sku'  => !empty($item['product_sku']) ? strip_tags(trim($item['product_sku'])) : 'N/A',
                    'brand_name'   => !empty($item['brand_name']) ? strip_tags(trim($item['brand_name'])) : 'MISUTECH',
                    'unit'         => !empty($item['unit']) ? strip_tags(trim($item['unit'])) : 'Cái',
                    'quantity'     => (int) $item['quantity'],
                    'unit_price'   => (float) $item['unit_price'],
                    'total_price'  => (float) $item['total_price'],
                ]);
            }

            DB::commit();
            RateLimiter::hit($rateLimitKey, 300);

            return response()->json([
                'success'    => true,
                'quote_code' => $quoteCode,
                'created_at' => $quote->created_at->format('d/m/Y H:i'),
                'message'    => 'Tạo và lưu bản báo giá thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi lưu bảng báo giá: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể lưu bản báo giá vào hệ thống. Tuy nhiên bạn vẫn có thể in hoặc xuất file PDF trực tiếp.'
            ], 500);
        }
    }
}
