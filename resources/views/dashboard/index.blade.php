@extends('layouts.app')
@section('title', 'Dashboard — AutoTerra')
@section('body')
@include('partials.nav')

<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  @if(session('success'))
  <div style="background:#F0FFF8;border:1px solid #6EE7B7;border-radius:8px;padding:12px 18px;margin-bottom:24px;font-size:14px;color:#065F46;">
    <i class="ti ti-check-circle"></i> {{ session('success') }}
  </div>
  @endif

  <h1 style="font-size:28px;font-weight:800;color:var(--body);margin-bottom:8px;">Welcome back, {{ Auth::user()->name }}</h1>
  <p style="font-size:14px;color:var(--muted);margin-bottom:32px;">Manage your subscriptions, licenses, and billing from here.</p>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:40px;">
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
      <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Active Subscriptions</div>
      <div style="font-size:32px;font-weight:800;color:var(--cyan);">{{ $activeSubscriptions->count() }}</div>
    </div>
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
      <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Active Licenses</div>
      <div style="font-size:32px;font-weight:800;color:var(--cyan);">{{ $activeLicenses->count() }}</div>
    </div>
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
      <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Total Orders</div>
      <div style="font-size:32px;font-weight:800;color:var(--cyan);">{{ $recentOrders->count() }}</div>
    </div>
  </div>

  @if($activeSubscriptions->count())
  <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;">Active Subscriptions</h2>
  <table style="width:100%;border-collapse:collapse;margin-bottom:40px;">
    <tr style="background:var(--navy);color:#fff;">
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Product</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Term</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Status</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Actions</th>
    </tr>
    @foreach($activeSubscriptions as $sub)
    <tr style="border-bottom:1px solid var(--border);">
      <td style="padding:12px 14px;font-size:13px;font-weight:700;">{{ $sub->product->name ?? 'N/A' }}</td>
      <td style="padding:12px 14px;font-size:13px;">{{ termLabel($sub->term) }}</td>
      <td style="padding:12px 14px;"><span style="background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ ucfirst($sub->status) }}</span></td>
      <td style="padding:12px 14px;">
        <form method="POST" action="{{ route('dashboard.subscription.cancel', $sub) }}" onsubmit="return confirm('Cancel this subscription?');">
          @csrf
          <button type="submit" style="background:none;border:1px solid #EF4444;color:#EF4444;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">Cancel</button>
        </form>
      </td>
    </tr>
    @endforeach
  </table>
  @endif

  <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;">Recent Orders</h2>
  <table style="width:100%;border-collapse:collapse;">
    <tr style="background:var(--navy);color:#fff;">
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Product</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Term</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Amount</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Status</th>
    </tr>
    @forelse($recentOrders as $order)
    <tr style="border-bottom:1px solid var(--border);">
      <td style="padding:12px 14px;font-size:13px;font-weight:700;">{{ $order->product->name ?? 'N/A' }}</td>
      <td style="padding:12px 14px;font-size:13px;">{{ termLabel($order->term) }}</td>
      <td style="padding:12px 14px;font-size:13px;">{{ $order->currency === 'INR' ? '₹' . number_format($order->total_amount, 0, '.', ',') : '$' . number_format($order->total_amount, 2, '.', ',') }}</td>
      <td style="padding:12px 14px;"><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $order->status === 'paid' ? '#D1FAE5' : ($order->status === 'failed' ? '#FEE2E2' : '#FEF3C7') }};color:{{ $order->status === 'paid' ? '#065F46' : ($order->status === 'failed' ? '#991B1B' : '#92400E') }};">{{ ucfirst($order->status) }}</span></td>
    </tr>
    @empty
    <tr><td colspan="4" style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">No orders yet. <a href="/buy" style="color:var(--cyan);">Browse products →</a></td></tr>
    @endforelse
  </table>
</div>
</div>

@include('partials.footer')
@endsection
