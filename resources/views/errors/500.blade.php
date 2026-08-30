@extends('errors.layout')

@section('title', '500 – Lỗi Máy Chủ Nội Bộ - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@section('badge_text', 'LỖI HỆ THỐNG MÁY CHỦ')
@section('badge_class', 'badge_danger')
@section('code', '500')
@section('code_class', 'code_500')

@section('message_title', 'Hệ thống đang được bảo trì hoặc xử lý quá tải!')
@section('message_desc', 'Máy chủ đang xử lý một lượng lớn yêu cầu hoặc đang tự động nâng cấp dữ liệu. Đội ngũ kỹ thuật MISUTECH đã ghi nhận sự cố và đang khắc phục ngay lập tức.')
