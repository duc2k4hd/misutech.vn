<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Hiển thị trang quản lý liên hệ từ khách hàng.
     */
    public function index()
    {
        return view('admins.pages.contacts.index');
    }

    /**
     * API Lấy danh sách liên hệ kèm phân trang & bộ lọc.
     */
    public function apiList(Request $request)
    {
        try {
            $query = Contact::query();

            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function($q) use ($kw) {
                    $q->where('name', 'like', "%{$kw}%")
                      ->orWhere('phone', 'like', "%{$kw}%")
                      ->orWhere('email', 'like', "%{$kw}%")
                      ->orWhere('subject', 'like', "%{$kw}%")
                      ->orWhere('message', 'like', "%{$kw}%");
                });
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $contacts = $query->orderBy('created_at', 'desc')->get();

            // Thống kê nhanh
            $stats = [
                'total'     => Contact::count(),
                'pending'   => Contact::where('status', 'pending')->count(),
                'contacted' => Contact::where('status', 'contacted')->count(),
                'completed' => Contact::where('status', 'completed')->count(),
            ];

            return response()->json([
                'data'  => array_values($contacts->toArray()),
                'stats' => $stats
            ]);
        } catch (\Throwable $e) {
            \Log::error('Contact apiList error: ' . $e->getMessage());
            return response()->json([
                'data'  => [],
                'stats' => ['total' => 0, 'pending' => 0, 'contacted' => 0, 'completed' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API Chi tiết một liên hệ.
     */
    public function apiShow($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json(['data' => $contact]);
    }

    /**
     * API Cập nhật trạng thái liên hệ.
     */
    public function apiUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,processing,completed,cancelled',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái liên hệ thành công!',
            'data'    => $contact
        ]);
    }

    /**
     * API Xóa liên hệ.
     */
    public function apiDestroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa liên hệ thành công!'
        ]);
    }

    /**
     * API Xóa hàng loạt liên hệ.
     */
    public function apiBulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:contacts,id'
        ]);

        Contact::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa các liên hệ được chọn!'
        ]);
    }
}
