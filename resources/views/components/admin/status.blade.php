@props(['status'])
@php
    $labels = ['draft' => 'ฉบับร่าง', 'submitted' => 'รอพิจารณา', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'expired' => 'หมดอายุ', 'active' => 'ใช้งานอยู่', 'disabled' => 'ปิดใช้งาน'];
    $icon = match ($status) { 'approved', 'active' => 'check', 'submitted', 'expired' => 'clock', 'rejected' => 'close', default => 'info' };
@endphp
<span class="pill pill-{{ $status }}"><x-admin.icon :name="$icon" size="13" /> {{ $labels[$status] ?? $status }}</span>
