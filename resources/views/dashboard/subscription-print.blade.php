<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscription — {{ $subscription->product->name }} — AutoTerra</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/plus-jakarta-sans@5/index.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #f5f8fc; color: #1C2B3A; }

    .print-controls { position: fixed; top: 0; left: 0; right: 0; background: #0B1522; padding: 14px 40px; display: flex; align-items: center; justify-content: space-between; z-index: 100; }
    .print-controls p { color: rgba(210,230,248,0.6); font-size: 14px; }
    .print-controls button { background: #00A8F8; color: #fff; border: none; border-radius: 7px; padding: 10px 24px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; }
    .print-controls button:hover { background: #007EC0; }
    .print-controls a { color: rgba(210,230,248,0.5); font-size: 13px; text-decoration: none; }

    .invoice { max-width: 800px; margin: 80px auto 40px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 20px rgba(0,0,0,0.06); }

    .invoice-header { background: #0B1522; padding: 40px 48px; display: flex; justify-content: space-between; align-items: flex-start; }
    .invoice-logo { display: flex; align-items: baseline; }
    .invoice-logo .a { color: #fff; font-size: 28px; font-weight: 800; }
    .invoice-logo .t { color: #00A8F8; font-size: 28px; font-weight: 800; }
    .invoice-meta { text-align: right; }
    .invoice-meta h2 { color: #fff; font-size: 22px; font-weight: 800; margin-bottom: 8px; }
    .invoice-meta p { color: rgba(210,230,248,0.45); font-size: 13px; line-height: 1.6; }

    .invoice-body { padding: 40px 48px; }

    .invoice-top { display: flex; justify-content: space-between; margin-bottom: 36px; gap: 40px; }
    .invoice-billed-to h4, .invoice-info h4 { font-size: 10px; font-weight: 700; color: #5A7A96; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .invoice-billed-to p { font-size: 14px; color: #1C2B3A; line-height: 1.6; }
    .invoice-billed-to .name { font-weight: 700; }
    .invoice-info { text-align: right; }
    .invoice-info table { margin-left: auto; }
    .invoice-info td { font-size: 13px; padding: 3px 0; }
    .invoice-info td:first-child { color: #5A7A96; padding-right: 16px; }
    .invoice-info td:last-child { font-weight: 600; }

    .invoice-status { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .status-active { background: #D1FAE5; color: #065F46; }
    .status-cancelled { background: #FEE2E2; color: #991B1B; }
    .status-paused { background: #FEF3C7; color: #92400E; }

    .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
    .invoice-table thead th { background: #F5F8FC; padding: 12px 16px; font-size: 10px; font-weight: 700; color: #5A7A96; text-transform: uppercase; letter-spacing: 0.8px; text-align: left; border-bottom: 2px solid #D2DCE6; }
    .invoice-table thead th:last-child { text-align: right; }
    .invoice-table tbody td { padding: 16px; font-size: 14px; border-bottom: 1px solid #F0F4F8; }
    .invoice-table tbody td:last-child { text-align: right; font-weight: 600; }
    .invoice-table tbody tr:last-child td { border-bottom: 2px solid #D2DCE6; }
    .item-name { font-weight: 700; }
    .item-desc { font-size: 12px; color: #5A7A96; margin-top: 2px; }

    .invoice-totals { display: flex; justify-content: flex-end; margin-bottom: 36px; }
    .totals-table { width: 280px; }
    .totals-table td { padding: 8px 0; font-size: 13px; }
    .totals-table td:first-child { color: #5A7A96; }
    .totals-table td:last-child { text-align: right; font-weight: 600; }
    .totals-table .divider td { padding: 4px 0; border-top: 1px solid #D2DCE6; }
    .totals-table .total td { font-size: 18px; font-weight: 800; color: #0B1522; padding-top: 12px; }

    .payment-info { background: #F5F8FC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px 24px; margin-bottom: 32px; }
    .payment-info h4 { font-size: 11px; font-weight: 700; color: #5A7A96; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 10px; }
    .payment-info table td { font-size: 13px; padding: 4px 0; }
    .payment-info table td:first-child { color: #5A7A96; padding-right: 20px; }
    .payment-info table td:last-child { font-weight: 600; }

    .invoice-footer { padding: 24px 48px; background: #F5F8FC; border-top: 1px solid #E2E8F0; text-align: center; }
    .invoice-footer p { font-size: 12px; color: #5A7A96; line-height: 1.6; }
    .invoice-footer a { color: #00A8F8; text-decoration: none; }

    @media print {
      .print-controls { display: none !important; }
      body { background: #fff; }
      .invoice { margin: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
      .invoice-header { padding: 32px 40px; }
      .invoice-body { padding: 32px 40px; }
      .invoice-footer { padding: 20px 40px; }
      @page { margin: 0; size: A4; }
    }
  </style>
</head>
<body>

<div class="print-controls">
  <a href="/dashboard/subscriptions/{{ $subscription->id }}">← Back to subscription</a>
  <p>{{ $subscription->product->name ?? 'Subscription' }}</p>
  <button onclick="window.print()">Print</button>
</div>

<div class="invoice">
  <div class="invoice-header">
    <div>
      <div class="invoice-logo"><span class="a">AUTO</span><span class="t">TERRA</span></div>
      <p style="color:rgba(210,230,248,0.35);font-size:12px;margin-top:6px;">Professional geospatial software</p>
    </div>
    <div class="invoice-meta">
      <h2>SUBSCRIPTION</h2>
      <p>{{ $subscription->product->name ?? 'Product' }}<br>
      {{ termLabel($subscription->term) }} plan<br>
      <span class="invoice-status status-{{ $subscription->status }}">{{ ucfirst($subscription->status) }}</span></p>
    </div>
  </div>

  <div class="invoice-body">
    <div class="invoice-top">
      <div class="invoice-billed-to">
        <h4>Subscriber</h4>
        <p class="name">{{ $subscription->user->name }}</p>
        <p>{{ $subscription->user->email }}</p>
        @if($subscription->user->company)
        <p>{{ $subscription->user->company }}</p>
        @endif
      </div>
      <div class="invoice-info">
        <h4>Subscription Details</h4>
        <table>
          <tr><td>Start Date</td><td>{{ $subscription->created_at->format('M j, Y') }}</td></tr>
          @if($subscription->current_period_start)
          <tr><td>Period Start</td><td>{{ $subscription->current_period_start->format('M j, Y') }}</td></tr>
          @endif
          @if($subscription->current_period_end)
          <tr><td>Next Billing</td><td>{{ $subscription->current_period_end->format('M j, Y') }}</td></tr>
          @endif
          <tr><td>Currency</td><td>{{ $subscription->currency }}</td></tr>
        </table>
      </div>
    </div>

    <table class="invoice-table">
      <thead>
        <tr>
          <th style="width:55%;">Description</th>
          <th>Billing Cycle</th>
          <th style="text-align:right;">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div class="item-name">{{ $subscription->product->name ?? 'Product' }}</div>
            <div class="item-desc">Recurring {{ termLabel($subscription->term) }} subscription</div>
          </td>
          <td>{{ termLabel($subscription->term) }}</td>
          <td>{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="invoice-totals">
      <table class="totals-table">
        <tr><td>Per {{ strtolower(termLabel($subscription->term)) }}</td><td>{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }}</td></tr>
        <tr class="divider"><td></td><td></td></tr>
        <tr class="total"><td>Total</td><td>{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }} / {{ $subscription->term }}</td></tr>
      </table>
    </div>

    <div class="payment-info">
      <h4>Subscription Information</h4>
      <table>
        <tr><td>Status</td><td><span class="invoice-status status-{{ $subscription->status }}">{{ ucfirst($subscription->status) }}</span></td></tr>
        @if($subscription->razorpay_subscription_id)
        <tr><td>Subscription ID</td><td>{{ $subscription->razorpay_subscription_id }}</td></tr>
        @endif
        @if($subscription->razorpay_plan_id)
        <tr><td>Plan ID</td><td>{{ $subscription->razorpay_plan_id }}</td></tr>
        @endif
        <tr><td>Payment Gateway</td><td>Razorpay</td></tr>
      </table>
    </div>
  </div>

  <div class="invoice-footer">
    <p><strong>AutoTerra</strong> by Infyterra Technologies · <a href="https://autoterra.net">autoterra.net</a></p>
    <p style="margin-top:4px;">F-2104, 1st Floor, Tower B, Ardent Office One, Hoodi, Bangalore 560048, Karnataka, India</p>
    <p style="margin-top:4px;">Questions? Contact <a href="mailto:support@autoterra.net">support@autoterra.net</a></p>
  </div>
</div>

</body>
</html>
