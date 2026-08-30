@extends('errors.layout')

@section('title', '404 – Không Tìm Thấy Trang - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@section('badge_text', 'LỖI ĐƯỜNG DẪN TRANG')
@section('badge_class', 'badge_warning')
@section('code', '404')
@section('code_class', '')

@section('message_title', 'Rất tiếc, trang bạn tìm kiếm không tồn tại!')
@section('message_desc', 'Đường dẫn bạn vừa truy cập có thể đã được chuyển đổi sang danh mục mới, bị gỡ bỏ hoặc bạn nhập chưa chính xác ký tự. Đừng lo lắng, hãy sử dụng thanh tìm kiếm bên dưới hoặc quay lại cửa hàng để tiếp tục.')
