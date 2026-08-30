@extends('errors.layout')

@section('title', '403 – Truy Cập Bị Từ Chối - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@section('badge_text', 'BẢO MẬT HỆ THỐNG')
@section('badge_class', 'badge_danger')
@section('code', '403')
@section('code_class', 'code_403')

@section('message_title', 'Truy cập bị từ chối (Forbidden)!')
@section('message_desc', 'Địa chỉ IP hoặc tài khoản của bạn chưa được cấp quyền để truy cập vào tài nguyên này. Nếu bạn là đối tác cần lấy tài liệu độc quyền, vui lòng liên hệ trực tiếp kỹ sư MISUTECH.')
