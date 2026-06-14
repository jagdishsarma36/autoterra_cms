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
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $order->status === 'paid' ? '#D1FAE5' : ($order->status === 'failed' ? '#FEE2E2' : '#FEF3C7') }};color:{{ $order->status === 'paid' ? '#065F46' : ($order->status === 'failed' ? '#991B1B' : '#92400E') }};">{{ ucfirst($order->status) }}</span>
      @if(\App\Models\Setting::get('user_can_print', true))
      <button onclick="window.open('/dashboard/orders/{{ $order->id }}/print', '_blank')" style="background:var(--cyan);color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="ti ti-printer"></i> Print Invoice</button>
      @endif
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;">
      <h3 style="font-size:14px;font-weight:800;margin-bottom:16px;">Order Details</h3>
      <table style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);width:40%;">Product</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $order->product->name ?? 'N/A' }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Term</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ termLabel($order->term) }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Status</td><td style="padding:8px 0;font-size:13px;font-weight:700;color:{{ $order->status === 'paid' ? '#065F46' : ($order->status === 'failed' ? '#991B1B' : '#92400E') }};">{{ ucfirst($order->status) }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Date</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $order->created_at->format('M j, Y g:i A') }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Billing Mode</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ ucfirst($order->billing_mode ?? 'N/A') }}</td></tr>
      </table>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;">
      <h3 style="font-size:14px;font-weight:800;margin-bottom:16px;">Payment Details</h3>
      <table style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);width:40%;">Amount</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $order->currency === 'INR' ? '₹' . number_format($order->amount, 0) : '$' . number_format($order->amount, 2) }}</td></tr>
        @if($order->gst_amount > 0)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">GST (18%)</td><td style="padding:8px 0;font-size:13px;font-weight:700;">₹{{ number_format($order->gst_amount, 0) }}</td></tr>
        @endif
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Total</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $order->currency === 'INR' ? '₹' . number_format($order->total_amount, 0) : '$' . number_format($order->total_amount, 2) }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Currency</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $order->currency }}</td></tr>
        @if($order->razorpay_order_id)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Razorpay Order ID</td><td style="padding:8px 0;font-size:11px;font-weight:600;word-break:break-all;">{{ $order->razorpay_order_id }}</td></tr>
        @endif
        @if($order->razorpay_payment_id)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Payment ID</td><td style="padding:8px 0;font-size:11px;font-weight:600;word-break:break-all;">{{ $order->razorpay_payment_id }}</td></tr>
        @endif
      </table>
    </div>
  </div>

  @if($order->licenseKeys->count())
  <div style="background:#F0FAFF;border:1.5px solid #B3E0FF;border-radius:12px;padding:24px;margin-top:24px;">
    <h3 style="font-size:14px;font-weight:800;margin-bottom:12px;color:#0860B0;">License Keys</h3>
    @foreach($order->licenseKeys as $license)
    <div style="background:#fff;border:1px solid #D2DCE6;border-radius:8px;padding:14px 18px;margin-bottom:10px;">
      <div style="font-family:monospace;font-size:14px;font-weight:700;letter-spacing:1px;color:var(--body);">{{ $license->license_key }}</div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px;">Valid until {{ $license->expires_at?->format('M j, Y') ?? 'N/A' }}</div>
    </div>
    @endforeach
  </div>
  @endif
</div>
</div>

@include('partials.footer')
@endsection
