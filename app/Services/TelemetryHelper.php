<?php

namespace App\Services;

use Illuminate\Http\Request;

class TelemetryHelper
{
    /**
     * Phân tích và trích xuất toàn bộ thông tin thiết bị, mạng và hành vi của người dùng.
     */
    public static function extract(Request $request): array
    {
        $userAgent = $request->userAgent() ?? '';
        $referer = $request->headers->get('referer') ?? $request->input('_client_referer');

        // 1. Phân tích loại thiết bị (Device Type)
        $deviceType = 'desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|mobile)/i', $userAgent)) {
            $deviceType = 'mobile';
        }

        // 2. Phân tích Hệ điều hành (Operating System)
        $os = 'Unknown OS';
        if (preg_match('/windows|win32|win98|win95|win_nt/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        // 3. Phân tích Trình duyệt (Browser)
        $browser = 'Unknown Browser';
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/safari/i', $userAgent)) {
            $browser = 'Apple Safari';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/coccoc/i', $userAgent)) {
            $browser = 'CocCoc';
        }

        // 4. Nhận các thông số Client-side (Màn hình, Múi giờ, Ngôn ngữ)
        $screen = $request->input('_client_screen', 'Unknown');
        $language = $request->input('_client_language', $request->getPreferredLanguage() ?? 'vi');
        $timezone = $request->input('_client_timezone', 'Asia/Ho_Chi_Minh');

        $meta = [
            'browser'          => $browser,
            'os'               => $os,
            'screen'           => $screen,
            'language'         => $language,
            'timezone'         => $timezone,
            'referer'          => $referer,
            'submitted_at_utc' => now()->toISOString(),
            'client_timestamp' => $request->input('_client_time'),
            'session_id'       => $request->hasSession() ? $request->session()->getId() : null,
        ];

        return [
            'ip_address'       => $request->ip(),
            'user_agent'       => mb_substr($userAgent, 0, 500),
            'referer'          => $referer ? mb_substr($referer, 0, 255) : null,
            'device_type'      => $deviceType,
            'duration_seconds' => (int) $request->input('duration_seconds', 0),
            'meta_data'        => $meta,
        ];
    }
}
