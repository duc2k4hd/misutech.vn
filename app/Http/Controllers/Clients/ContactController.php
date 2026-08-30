<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Hiển thị trang liên hệ & hỗ trợ kỹ thuật.
     */
    public function index()
    {
        // Tạo token timestamp mã hóa cho time-gate
        $formToken = Crypt::encryptString((string) time());
        return view('clients.pages.contact.index', compact('formToken'));
    }

    /**
     * Xử lý gửi yêu cầu liên hệ / báo giá từ khách hàng với các lớp bảo mật & chống spam.
     */
    public function submit(Request $request)
    {
        $ip = $request->ip();
        $rateLimitKey = 'contact-submit:' . $ip;

        // 1. Rate Limiting: Tối đa 3 lần gửi trong 5 phút trên 1 IP
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $msg = 'Bạn đã gửi nhiều yêu cầu liên hệ trong thời gian ngắn. Vui lòng chờ ' . ceil($seconds / 60) . ' phút nữa hoặc liên hệ trực tiếp qua Hotline / Zalo.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withInput()->withErrors(['rate_limit' => $msg]);
        }

        // 2. Honeypot Anti-Bot Check (Trường bẫy vô hình)
        // Bot tự động điền các trường như website / company_hp
        $honeypot = $request->input('company_hp_url');
        if (!empty($honeypot)) {
            // Giả lập thành công để bot không tiếp tục brute-force
            RateLimiter::hit($rateLimitKey, 300);
            Log::warning("Spam bot detected by honeypot from IP: {$ip}");
            return $this->respondSuccess($request);
        }

        // 3. Time-gate Check (Kiểm tra thời gian hoàn thành form)
        $formToken = $request->input('_form_time_token');
        if (!empty($formToken)) {
            try {
                $renderedTime = (int) Crypt::decryptString($formToken);
                $timeDiff = time() - $renderedTime;
                
                // Submit dưới 2 giây -> Khả năng cao là script bot tự động
                if ($timeDiff < 2) {
                    RateLimiter::hit($rateLimitKey, 300);
                    Log::warning("Spam bot detected by fast submission ({$timeDiff}s) from IP: {$ip}");
                    return $this->respondSuccess($request);
                }

                // Form đã mở quá 24 tiếng (hết hạn session)
                if ($timeDiff > 86400) {
                    return back()->withInput()->withErrors(['form_expired' => 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng tải lại trang và gửi lại.']);
                }
            } catch (\Exception $e) {
                // Token không hợp lệ
                RateLimiter::hit($rateLimitKey, 300);
                return $this->respondSuccess($request);
            }
        }

        // 4. Validation nghiêm ngặt & Tiêu chuẩn hóa dữ liệu
        $validated = $request->validate([
            'fullname' => [
                'required',
                'string',
                'min:2',
                'max:80',
                'regex:/^[\p{L}\s\.\-]+$/u' // Chỉ cho phép chữ cái, khoảng trắng, dấu chấm, dấu gạch ngang
            ],
            'phone'    => [
                'required',
                'string',
                'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/' // Định dạng số di động Việt Nam chuẩn
            ],
            'email'    => [
                'nullable',
                'email:rfc',
                'max:90'
            ],
            'subject'  => [
                'nullable',
                'string',
                'max:120'
            ],
            'message'  => [
                'required',
                'string',
                'min:5',
                'max:1500'
            ],
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'fullname.regex'    => 'Họ và tên chỉ được chứa chữ cái.',
            'fullname.max'      => 'Họ và tên không vượt quá 80 ký tự.',
            'phone.required'    => 'Vui lòng nhập số điện thoại hoặc Zalo liên hệ.',
            'phone.regex'       => 'Số điện thoại không đúng định dạng (Ví dụ: 0866555212 hoặc +84866555212).',
            'email.email'       => 'Địa chỉ email không hợp lệ.',
            'message.required'  => 'Vui lòng nhập nội dung cần tư vấn hoặc báo giá.',
            'message.min'       => 'Nội dung quá ngắn (tối thiểu 5 ký tự).',
            'message.max'       => 'Nội dung không được vượt quá 1500 ký tự.',
        ]);

        // 5. Anti-Spam Content Filter (Lọc nội dung chứa link rác / từ khóa độc hại)
        $rawMessage = $validated['message'];
        $linkCount = preg_match_all('/https?:\/\//i', $rawMessage);
        if ($linkCount >= 3) {
            RateLimiter::hit($rateLimitKey, 300);
            Log::warning("Spam content detected (excessive links) from IP: {$ip}");
            return $this->respondSuccess($request);
        }

        // Lọc từ khóa spam cờ bạc / lừa đảo quốc tế
        $spamKeywords = ['casino', 'viagra', 'cryptocurrency', 'poker', 't.me/', 'bit.ly/'];
        foreach ($spamKeywords as $kw) {
            if (stripos($rawMessage, $kw) !== false) {
                RateLimiter::hit($rateLimitKey, 300);
                Log::warning("Spam keyword '{$kw}' detected from IP: {$ip}");
                return $this->respondSuccess($request);
            }
        }

        // 6. Làm sạch dữ liệu chống XSS (Sanitize)
        $cleanName    = strip_tags(trim($validated['fullname']));
        $cleanPhone   = strip_tags(trim($validated['phone']));
        $cleanEmail   = !empty($validated['email']) ? strip_tags(trim($validated['email'])) : 'no-email@misutech.vn';
        $cleanSubject = !empty($validated['subject']) ? strip_tags(trim($validated['subject'])) : 'Yêu cầu tư vấn / Báo giá';
        $cleanMessage = strip_tags(trim($validated['message']));

        // 7. Thu thập toàn bộ Telemetry (IP, Thiết bị, Trình duyệt, Nguồn truy cập, Thời gian)
        $telemetry = \App\Services\TelemetryHelper::extract($request);
        if (isset($timeDiff)) {
            $telemetry['duration_seconds'] = $timeDiff;
        }

        // 8. Lưu an toàn vào Database
        try {
            Contact::create([
                'name'             => Str::limit($cleanName, 180, ''),
                'phone'            => Str::limit($cleanPhone, 50, ''),
                'email'            => Str::limit($cleanEmail, 180, ''),
                'subject'          => Str::limit($cleanSubject, 180, ''),
                'message'          => $cleanMessage,
                'status'           => 'pending',
                'ip_address'       => $telemetry['ip_address'],
                'user_agent'       => $telemetry['user_agent'],
                'referer'          => $telemetry['referer'],
                'device_type'      => $telemetry['device_type'],
                'duration_seconds' => $telemetry['duration_seconds'],
                'meta_data'        => $telemetry['meta_data'],
            ]);

            // Ghi nhận 1 lượt submit thành công
            RateLimiter::hit($rateLimitKey, 300);

            return $this->respondSuccess($request);

        } catch (\Exception $e) {
            // Không làm lộ chi tiết lỗi database ra ngoài
            Log::error("Lỗi lưu liên hệ: " . $e->getMessage());

            $errorMsg = 'Hệ thống đang bận. Vui lòng liên hệ trực tiếp qua Hotline: 0866.555.212 để được hỗ trợ ngay lập tức.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            return back()->withInput()->withErrors(['system' => $errorMsg]);
        }
    }

    /**
     * Trả về kết quả thành công an toàn.
     */
    private function respondSuccess(Request $request)
    {
        $successMsg = 'Cảm ơn bạn đã gửi yêu cầu! Đội ngũ kỹ sư MISUTECH đã tiếp nhận và sẽ liên hệ tư vấn trong thời gian sớm nhất.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg
            ]);
        }

        return redirect()->route('contact.index')->with('success', $successMsg);
    }
}
