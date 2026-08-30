<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportContact;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SupportContactController extends Controller
{
    /**
     * Hiển thị trang quản lý Danh bạ Tư vấn & Hotline.
     */
    public function index()
    {
        return view('admins.pages.support_contacts.index');
    }

    /**
     * API Lấy danh sách nhân viên tư vấn & dịch vụ hỗ trợ.
     */
    public function apiList(Request $request)
    {
        $query = SupportContact::query();

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('phone', 'like', "%{$kw}%")
                  ->orWhere('department', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('department_type')) {
            $query->where('department_type', $request->department_type);
        }

        $contacts = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['data' => $contacts]);
    }

    /**
     * API Thêm mới hoặc Cập nhật nhân viên tư vấn / hotline.
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|string|max:30',
            'zalo_phone'      => 'nullable|string|max:30',
            'department'      => 'required|string|max:100',
            'department_type' => 'required|in:sale,technical,warranty,other',
            'note'            => 'nullable|string|max:255',
            'sort_order'      => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
            'show_in_popup'   => 'nullable|boolean',
            'avatar_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'name.required'       => 'Vui lòng nhập tên nhân viên hoặc tên phòng ban.',
            'phone.required'      => 'Vui lòng nhập số điện thoại hotline/call.',
            'department.required' => 'Vui lòng nhập phòng ban hoặc vị trí hỗ trợ.',
        ]);

        $avatar = null;
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = 'support_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/clients/imgs/support');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $avatar = $filename;
        } elseif ($request->id) {
            $existing = SupportContact::find($request->id);
            $avatar = $existing ? $existing->avatar : null;
        }

        $contact = SupportContact::updateOrCreate(
            ['id' => $request->id],
            [
                'name'            => strip_tags(trim($request->name)),
                'phone'           => preg_replace('/[^0-9\+]/', '', trim($request->phone)),
                'zalo_phone'      => $request->filled('zalo_phone') ? preg_replace('/[^0-9\+]/', '', trim($request->zalo_phone)) : preg_replace('/[^0-9\+]/', '', trim($request->phone)),
                'department'      => strip_tags(trim($request->department)),
                'department_type' => $request->department_type,
                'avatar'          => $avatar,
                'note'            => strip_tags(trim($request->note ?? '')),
                'sort_order'      => (int) ($request->sort_order ?? 0),
                'is_active'       => $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : true,
                'show_in_popup'   => $request->has('show_in_popup') ? filter_var($request->show_in_popup, FILTER_VALIDATE_BOOLEAN) : true,
            ]
        );

        Cache::forget('global_support_contacts');

        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Cập nhật nhân viên tư vấn thành công!' : 'Thêm mới nhân viên tư vấn thành công!',
            'data'    => $contact
        ]);
    }

    /**
     * API Chi tiết một nhân viên tư vấn.
     */
    public function apiShow($id)
    {
        $contact = SupportContact::findOrFail($id);
        return response()->json(['data' => $contact]);
    }

    /**
     * API Xóa nhân viên tư vấn.
     */
    public function apiDestroy($id)
    {
        $contact = SupportContact::findOrFail($id);
        $contact->delete();

        Cache::forget('global_support_contacts');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa nhân viên tư vấn thành công!'
        ]);
    }

    /**
     * API Bật/tắt nhanh trạng thái.
     */
    public function apiToggleStatus(Request $request, $id)
    {
        $contact = SupportContact::findOrFail($id);
        $field = $request->input('field', 'is_active');

        if (in_array($field, ['is_active', 'show_in_popup'])) {
            $contact->$field = !$contact->$field;
            $contact->save();
            Cache::forget('global_support_contacts');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'data'    => $contact
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Trường không hợp lệ'], 400);
    }
}
