<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f8fc; margin: 0; padding: 40px 0; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
    .header { background: #0B1522; padding: 32px 40px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .header h1 span { color: #00A8F8; }
    .body { padding: 36px 40px; }
    .body h2 { font-size: 18px; color: #1C2B3A; margin: 0 0 8px; }
    .body p { font-size: 14px; color: #5A7A96; line-height: 1.7; margin: 0 0 16px; }
    .details { background: #F5F8FC; border: 1px solid #D2DCE6; border-radius: 8px; padding: 20px; margin: 20px 0; }
    .details table { width: 100%; border-collapse: collapse; }
    .details td { padding: 8px 0; font-size: 13px; color: #1C2B3A; }
    .details td:first-child { color: #5A7A96; width: 40%; }
    .details td:last-child { font-weight: 600; }
    .badge { display: inline-block; background: #D1FAE5; color: #065F46; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .footer { padding: 24px 40px; background: #F5F8FC; border-top: 1px solid #D2DCE6; }
    .footer p { font-size: 12px; color: #5A7A96; margin: 0; line-height: 1.6; }
    .footer a { color: #00A8F8; }
    .btn { display: inline-block; background: #00A8F8; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-size: 14px; font-weight: 700; margin: 16px 0; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><span>AUTO</span>TERRA</h1>
    </div>
    <div class="body">
      <h2>Subscription Active</h2>
      <p>Your subscription has been successfully set up. Recurring payments will be processed automatically.</p>

      <div class="details">
        <table>
          <tr><td>Product</td><td>{{ $subscription->product->name }}</td></tr>
          <tr><td>Term</td><td>{{ termLabel($subscription->term) }}</td></tr>
          <tr><td>Status</td><td><span class="badge">Active</span></td></tr>
          <tr><td>Amount</td><td>{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }} / {{ $subscription->term }}</td></tr>
          @if($subscription->current_period_end)
          <tr><td>Next billing date</td><td>{{ $subscription->current_period_end->format('M j, Y') }}</td></tr>
          @endif
          @if($subscription->razorpay_subscription_id)
          <tr><td>Subscription ID</td><td style="font-size:11px;">{{ $subscription->razorpay_subscription_id }}</td></tr>
          @endif
        </table>
      </div>

      <a href="{{ config('app.url') }}/dashboard/subscriptions" class="btn">Manage Subscription</a>

      <p style="font-size:13px;">You can cancel your subscription at any time from your dashboard. Your access will continue until the end of the current billing period.</p>
    </div>
    <div class="footer">
      <p>AutoTerra by Infyterra Technologies · <a href="{{ config('app.url') }}">autoterra.net</a></p>
      <p style="margin-top:8px;">Questions? Reply to this email or contact <a href="mailto:support@autoterra.net">support@autoterra.net</a></p>
    </div>
  </div>
</body>
</html>
