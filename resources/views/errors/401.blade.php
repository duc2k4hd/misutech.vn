@extends('errors.layout')

@section('title', '401 – Yêu Cầu Xác Thực - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@section('badge_text', 'XÁC THỰC TRUY CẬP')
@section('badge_class', 'badge_warning')
@section('code', '401')
@section('code_class', '')

@section('message_title', 'Yêu cầu xác thực tài khoản để tiếp tục!')
@section('message_desc', 'Bạn cần đăng nhập hoặc cung cấp quyền truy cập hợp lệ để xem nội dung này. Phiên làm việc của bạn có thể đã hết hạn sau thời gian dài không thao tác.')
