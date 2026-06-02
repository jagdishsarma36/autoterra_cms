@extends('layouts.app')
@section('title', 'Subscriptions — AutoTerra')
@section('body')
@include('partials.nav')
<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  <h1 style="font-size:28px;font-weight:800;margin-bottom:24px;">My Subscriptions</h1>
  @forelse($subscriptions as $sub)
  <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;" onclick="window.location='/dashboard/subscriptions/{{ $sub->id }}'">
    <div>
      <div style="font-size:16px;font-weight:800;">{{ $sub->product->name ?? 'N/A' }}</div>
      <div style="font-size:13px;color:var(--muted);margin-top:4px;">{{ termLabel($sub->term) }} · {{ ucfirst($sub->currency) }} {{ $sub->currency === 'INR' ? '₹' . number_format($sub->amount, 0, '.', ',') : '$' . number_format($sub->amount, 2, '.', ',') }}</div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $sub->current_period_start?->format('M j, Y') }} — {{ $sub->current_period_end?->format('M j, Y') }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
      <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $sub->status === 'active' ? '#D1FAE5' : '#FEE2E2' }};color:{{ $sub->status === 'active' ? '#065F46' : '#991B1B' }};">{{ ucfirst($sub->status) }}</span>
      @if($sub->status === 'active')
      <form method="POST" action="{{ route('dashboard.subscription.cancel', $sub) }}" onsubmit="return confirm('Cancel this subscription? It will remain active until the end of the current billing period.');">
        @csrf
        <button type="submit" style="background:none;border:1px solid #EF4444;color:#EF4444;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">Cancel</button>
      </form>
      @endif
    </div>
  </div>
  @empty
  <div style="text-align:center;padding:60px 0;color:var(--muted);">
    <p>No subscriptions yet.</p>
    <a href="/buy" style="color:var(--cyan);font-weight:700;">Browse products →</a>
  </div>
  @endforelse
</div>
</div>

@include('partials.footer')
@endsection
