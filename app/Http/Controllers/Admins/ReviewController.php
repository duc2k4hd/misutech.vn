<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá trong trang Admin.
     */
    public function index()
    {
        return view('admins.pages.reviews.index');
    }

    /**
     * API danh sách đánh giá.
     */
    public function apiList(Request $request)
    {
        $query = Review::with(['product:id,name,slug,sku', 'user:id,name,email'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('author_name', 'like', "%{$search}%")
                  ->orWhere('author_phone', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
            });
        }

        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $reviews->items(),
            'meta'    => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
            ]
        ]);
    }

    /**
     * API cập nhật trạng thái duyệt đánh giá (approved, pending, rejected).
     */
    public function apiUpdateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,pending,rejected'
        ]);

        $review = Review::findOrFail($id);
        $oldStatus = $review->status;
        $review->status = $validated['status'];
        $review->save();

        // Tự động tính toán lại rating_average và reviews_count của Product khi duyệt
        $this->recalculateProductRating($review->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đánh giá thành công!',
            'status'  => $review->status
        ]);
    }

    /**
     * API xóa đánh giá.
     */
    public function apiDestroy($id)
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;
        $review->delete();

        // Tính lại điểm
        $this->recalculateProductRating($productId);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá thành công!'
        ]);
    }

    /**
     * Hàm helper tính lại điểm trung bình & số lượng review cho sản phẩm
     */
    private function recalculateProductRating($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $approvedReviews = Review::where('product_id', $productId)->where('status', 'approved');
        $count = $approvedReviews->count();
        $avg = $count > 0 ? round($approvedReviews->avg('rating'), 1) : 5.0;

        $product->update([
            'reviews_count'  => $count,
            'rating_average' => $avg,
        ]);
    }
}
