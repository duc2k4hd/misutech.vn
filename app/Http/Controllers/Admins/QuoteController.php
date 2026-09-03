<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\QuoteItem;

class QuoteController extends Controller
{
    /**
     * Hiển thị trang quản lý Báo giá dự án & sản phẩm.
     */
    public function index()
    {
        return view('admins.pages.quotes.index');
    }

    /**
     * API Lấy danh sách báo giá kèm bộ lọc.
     */
    public function apiList(Request $request)
    {
        try {
            $query = Quote::with('items');

            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function($q) use ($kw) {
                    $q->where('quote_code', 'like', "%{$kw}%")
                      ->orWhere('customer_name', 'like', "%{$kw}%")
                      ->orWhere('customer_phone', 'like', "%{$kw}%")
                      ->orWhere('customer_company', 'like', "%{$kw}%")
                      ->orWhere('customer_email', 'like', "%{$kw}%");
                });
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $quotes = $query->orderBy('created_at', 'desc')->get();

            // Thống kê nhanh
            $stats = [
                'total'          => Quote::count(),
                'total_amount'   => (float) Quote::sum('total_amount'),
                'draft'          => Quote::where('status', 'draft')->count(),
                'sent'           => Quote::where('status', 'sent')->count(),
                'confirmed'      => Quote::where('status', 'confirmed')->count(),
                'completed'      => Quote::where('status', 'completed')->count(),
            ];

            return response()->json([
                'data'  => array_values($quotes->toArray()),
                'stats' => $stats
            ]);
        } catch (\Throwable $e) {
            \Log::error('Quote apiList error: ' . $e->getMessage());
            return response()->json([
                'data'  => [],
                'stats' => ['total' => 0, 'total_amount' => 0, 'draft' => 0, 'sent' => 0, 'confirmed' => 0, 'completed' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API Chi tiết một báo giá kèm danh sách sản phẩm quote_items.
     */
    public function apiShow($id)
    {
        $quote = Quote::with('items.product')->findOrFail($id);
        return response()->json(['data' => $quote]);
    }

    /**
     * API Cập nhật trạng thái và ghi chú báo giá.
     */
    public function apiUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,confirmed,completed,cancelled',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $quote = Quote::findOrFail($id);
        $quote->status = $request->status;
        if ($request->has('notes')) {
            $quote->notes = $request->notes;
        }
        $quote->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái báo giá thành công!',
            'data'    => $quote
        ]);
    }

    /**
     * API Xóa báo giá.
     */
    public function apiDestroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->items()->delete();
        $quote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bản ghi báo giá thành công!'
        ]);
    }

    /**
     * API Xóa hàng loạt báo giá.
     */
    public function apiBulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:quotes,id'
        ]);

        QuoteItem::whereIn('quote_id', $request->ids)->delete();
        Quote::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa các bản ghi báo giá được chọn!'
        ]);
    }
}
