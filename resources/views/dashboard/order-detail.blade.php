@extends('layouts.app')
@section('title', 'Order #' . $order->id . ' — AutoTerra')
@section('body')
@include('partials.nav')

<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
    <div>
      <a href="/dashboard/orders" style="font-size:13px;color:var(--cyan);display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;"><i class="ti ti-arrow-left"></i> Back to orders</a>
      <h1 style="font-size:28px;font-weight:800;">Order #{{ $order->id }}</h1>
    </div>
    <div style="display:flex;gap:10px;">
      <span style="padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $order->status === 'paid' ? '#D1FAE5' : ($order->status === 'failed' ? '#FEE2E2' : '#FEF3C7') }};color:{{ $order->status === 'paid' ? '#065F46' : ($order->status === 'failed' ? '#991B1B' : '#92400E') }};">{{ ucfirst($order->status) }}</span>
      @if(\App\Models\Setting::get('user_can_print', true))
      <button onclick="window.open('/dashboard/orders/{{ $order->id }}/print', '_blank')" style="background:var(--cyan);color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="ti ti-printer"></i> Print Invoice</button>
      @endif
</div>
</div>

@include('partials.footer')
@endsection
