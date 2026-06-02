@extends('layouts.app')
@section('title', 'Subscription — AutoTerra')
@section('body')
@include('partials.nav')

<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
    <div>
      <a href="/dashboard/subscriptions" style="font-size:13px;color:var(--cyan);display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;"><i class="ti ti-arrow-left"></i> Back to subscriptions</a>
      <h1 style="font-size:28px;font-weight:800;">{{ $subscription->product->name ?? 'Subscription' }}</h1>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $subscription->status === 'active' ? '#D1FAE5' : '#FEE2E2' }};color:{{ $subscription->status === 'active' ? '#065F46' : '#991B1B' }};">{{ ucfirst($subscription->status) }}</span>
      @if(\App\Models\Setting::get('user_can_print', true))
      <button onclick="window.open('/dashboard/subscriptions/{{ $subscription->id }}/print', '_blank')" style="background:var(--cyan);color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="ti ti-printer"></i> Print</button>
      @endif
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;">
      <h3 style="font-size:14px;font-weight:800;margin-bottom:16px;">Subscription Details</h3>
      <table style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);width:40%;">Product</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->product->name ?? 'N/A' }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Term</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ termLabel($subscription->term) }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Status</td><td style="padding:8px 0;font-size:13px;font-weight:700;color:{{ $subscription->status === 'active' ? '#065F46' : '#991B1B' }};">{{ ucfirst($subscription->status) }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Started</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->created_at->format('M j, Y') }}</td></tr>
        @if($subscription->current_period_start)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Current Period Start</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->current_period_start->format('M j, Y') }}</td></tr>
        @endif
        @if($subscription->current_period_end)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Next Billing Date</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->current_period_end->format('M j, Y') }}</td></tr>
        @endif
      </table>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;">
      <h3 style="font-size:14px;font-weight:800;margin-bottom:16px;">Payment Details</h3>
      <table style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);width:40%;">Amount</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }} / {{ $subscription->term }}</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Currency</td><td style="padding:8px 0;font-size:13px;font-weight:700;">{{ $subscription->currency }}</td></tr>
        @if($subscription->razorpay_subscription_id)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Razorpay Sub ID</td><td style="padding:8px 0;font-size:11px;font-weight:600;word-break:break-all;">{{ $subscription->razorpay_subscription_id }}</td></tr>
        @endif
        @if($subscription->razorpay_plan_id)
        <tr><td style="padding:8px 0;font-size:13px;color:var(--muted);">Plan ID</td><td style="padding:8px 0;font-size:11px;font-weight:600;word-break:break-all;">{{ $subscription->razorpay_plan_id }}</td></tr>
        @endif
      </table>
    </div>
  </div>

  @if($subscription->status === 'active')
  <div style="background:#FEF3E2;border:1px solid #F0C97A;border-radius:12px;padding:24px;margin-top:24px;">
    <div style="display:flex;align-items:flex-start;gap:14px;">
      <i class="ti ti-alert-triangle" style="font-size:22px;color:var(--amber);flex-shrink:0;margin-top:2px;"></i>
      <div>
        <h4 style="font-size:14px;font-weight:700;margin-bottom:6px;">Cancel Subscription</h4>
        <p style="font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:12px;">If you cancel, your subscription will remain active until the end of the current billing period ({{ $subscription->current_period_end?->format('M j, Y') ?? 'N/A' }}). You will not be charged again after that.</p>
        <form method="POST" action="{{ route('dashboard.subscription.cancel', $subscription) }}" onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
          @csrf
          <button type="submit" style="background:#EF4444;color:#fff;border:none;border-radius:7px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;">Cancel Subscription</button>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>
</div>

@include('partials.footer')
@endsection
