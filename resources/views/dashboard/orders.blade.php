@extends('layouts.app')
@section('title', 'Orders — AutoTerra')
@section('body')
@include('partials.nav')
<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  <h1 style="font-size:28px;font-weight:800;margin-bottom:24px;">Billing History</h1>
  <table style="width:100%;border-collapse:collapse;">
    <tr style="background:var(--navy);color:#fff;">
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Date</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Product</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Term</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Amount</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Status</th>
    </tr>
    @forelse($orders as $order)
    <tr style="border-bottom:1px solid var(--border);cursor:pointer;" onclick="window.location='/dashboard/orders/{{ $order->id }}'">
      <td style="padding:12px 14px;font-size:13px;">{{ $order->created_at->format('M j, Y') }}</td>
      <td style="padding:12px 14px;font-size:13px;font-weight:700;">{{ $order->product->name ?? 'N/A' }}</td>
      <td style="padding:12px 14px;font-size:13px;">{{ termLabel($order->term) }}</td>
      <td style="padding:12px 14px;font-size:13px;">{{ $order->currency === 'INR' ? '₹' . number_format($order->total_amount, 0, '.', ',') : '$' . number_format($order->total_amount, 2, '.', ',') }}</td>
      <td style="padding:12px 14px;"><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $order->status === 'paid' ? '#D1FAE2' : ($order->status === 'failed' ? '#FEE2E2' : '#FEF3C7') }};color:{{ $order->status === 'paid' ? '#065F46' : ($order->status === 'failed' ? '#991B1B' : '#92400E') }};">{{ ucfirst($order->status) }}</span></td>
    </tr>
    @empty
    <tr><td colspan="5" style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">No orders yet.</td></tr>
    @endforelse
  </table>
</div>
</div>

@include('partials.footer')
@endsection
