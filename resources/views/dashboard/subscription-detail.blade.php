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

  @if($autoCancelled)
  <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:24px;margin-top:24px;">
    <div style="display:flex;align-items:flex-start;gap:14px;">
      <i class="ti ti-alert-circle" style="font-size:22px;color:#DC2626;flex-shrink:0;margin-top:2px;"></i>
      <div>
        <h4 style="font-size:14px;font-weight:700;margin-bottom:6px;color:#991B1B;">Subscription Auto-Cancelled</h4>
        <p style="font-size:13px;color:#7F1D1D;line-height:1.6;">This subscription was automatically cancelled because a payment remained pending for more than 7 days.</p>
      </div>
    </div>
  </div>
  @endif

  @if(!empty($invoices))
  <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;margin-top:24px;">
    <h3 style="font-size:14px;font-weight:800;margin-bottom:16px;">Payment History</h3>
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="border-bottom:2px solid var(--border);">
            <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Invoice</th>
            <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Date</th>
            <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Amount</th>
            <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
            <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoices as $inv)
          @php
            $invStatus = $inv['status'] ?? 'unknown';
            $invAmount = $inv['amount'] ?? 0;
            $invCurrency = $inv['currency'] ?? 'INR';
            $invCreated = isset($inv['created_at']) ? \Carbon\Carbon::createFromTimestamp($inv['created_at'])->format('M j, Y') : 'N/A';
            $invId = $inv['id'] ?? '';
            $invShortId = substr($invId, -8);
            $paidAt = isset($inv['paid_at']) ? \Carbon\Carbon::createFromTimestamp($inv['paid_at'])->format('M j, Y g:i A') : null;
            $periodStart = $inv['line_items'][0]['item']['name'] ?? null;
          @endphp
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:12px;font-weight:600;">
              <span style="font-family:monospace;font-size:12px;">INV-{{ strtoupper($invShortId) }}</span>
            </td>
            <td style="padding:12px;">{{ $invCreated }}</td>
            <td style="padding:12px;font-weight:700;">
              {{ $invCurrency === 'INR' ? '₹' . number_format($invAmount / 100, 0) : '$' . number_format($invAmount / 100, 2) }}
            </td>
            <td style="padding:12px;">
              @if($invStatus === 'paid')
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#D1FAE5;color:#065F46;">Paid</span>
              @elseif($invStatus === 'failed')
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#FEE2E2;color:#991B1B;">Failed</span>
              @elseif($invStatus === 'created')
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#FEF3E2;color:#92400E;">Pending</span>
              @elseif($invStatus === 'cancelled')
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#F3F4F6;color:#374151;">Cancelled</span>
              @elseif($invStatus === 'expired')
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#F3F4F6;color:#374151;">Expired</span>
              @else
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#F3F4F6;color:#374151;">{{ ucfirst($invStatus) }}</span>
              @endif
            </td>
            <td style="padding:12px;">
              @if($invStatus === 'failed' && $subscription->status === 'active')
                <div style="display:flex;gap:8px;">
                  <button onclick="retryPayment('{{ $invId }}', {{ $invAmount }}, '{{ $invCurrency }}')" style="background:var(--cyan);color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                    <i class="ti ti-refresh"></i> Retry
                  </button>
                  <form method="POST" action="{{ route('dashboard.subscription.cancel', $subscription) }}" onsubmit="return confirm('Cancel this subscription?');" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:#EF4444;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                      <i class="ti ti-x"></i> Cancel
                    </button>
                  </form>
                </div>
              @elseif($invStatus === 'paid' && $paidAt)
                <span style="font-size:11px;color:var(--muted);">Paid {{ $paidAt }}</span>
              @elseif($invStatus === 'created')
                <span style="font-size:11px;color:var(--muted);">Awaiting payment</span>
              @else
                <span style="font-size:11px;color:var(--muted);">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  @if($subscription->licenseKeys->count())
  <div style="background:#F0FAFF;border:1.5px solid #B3E0FF;border-radius:12px;padding:24px;margin-top:24px;">
    <h3 style="font-size:14px;font-weight:800;margin-bottom:12px;color:#0860B0;">License Keys</h3>
    @foreach($subscription->licenseKeys as $license)
    <div style="background:#fff;border:1px solid #D2DCE6;border-radius:8px;padding:14px 18px;margin-bottom:10px;">
      <div style="font-family:monospace;font-size:14px;font-weight:700;letter-spacing:1px;color:var(--body);">{{ $license->license_key }}</div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px;">Valid until {{ $license->expires_at?->format('M j, Y') ?? 'N/A' }}</div>
    </div>
    @endforeach
  </div>
  @endif

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

@section('scripts')
<script>
function retryPayment(invoiceId, amount, currency) {
  if (!confirm('Retry this payment?')) return;

  fetch('{{ route("dashboard.subscription.retry-invoice", $subscription) }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ invoice_id: invoiceId })
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) {
      alert('Error: ' + data.error);
      return;
    }

    var rzp = new Razorpay({
      key: data.key_id,
      amount: data.amount,
      currency: data.currency,
      name: 'AutoTerra',
      description: data.product_name + ' — Retry Payment',
      order_id: data.order_id,
      handler: function(response) {
        fetch('/api/razorpay/verify', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature,
            db_order_id: data.db_order_id
          })
        })
        .then(r => r.json())
        .then(vData => {
          if (vData.success) {
            window.location.reload();
          } else {
            alert('Payment verification failed.');
          }
        });
      },
      modal: {
        ondismiss: function() {
          alert('Payment was cancelled.');
        }
      },
      prefill: {
        name: data.user_name,
        email: data.user_email
      },
      theme: { color: '#00A8F8' }
    });
    rzp.open();
  })
  .catch(function() {
    alert('Something went wrong. Please try again.');
  });
}
</script>
@endsection
