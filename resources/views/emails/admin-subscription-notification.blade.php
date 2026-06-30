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
    .header .badge-admin { display: inline-block; background: #F59E0B; color: #fff; font-size: 11px; padding: 2px 10px; border-radius: 10px; margin-left: 8px; vertical-align: middle; }
    .body { padding: 36px 40px; }
    .body h2 { font-size: 18px; color: #1C2B3A; margin: 0 0 8px; }
    .body p { font-size: 14px; color: #5A7A96; line-height: 1.7; margin: 0 0 16px; }
    .status-update { background: #F5F8FC; border: 1px solid #D2DCE6; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
    .status-update .old-status { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; background: #D2DCE6; color: #5A7A96; }
    .status-update .arrow { display: inline-block; margin: 0 16px; font-size: 20px; color: #5A7A96; }
    .status-update .new-status { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; background: #00A8F8; color: #fff; }
    .details { background: #F5F8FC; border: 1px solid #D2DCE6; border-radius: 8px; padding: 20px; margin: 20px 0; }
    .details table { width: 100%; border-collapse: collapse; }
    .details td { padding: 8px 0; font-size: 13px; color: #1C2B3A; }
    .details td:first-child { color: #5A7A96; width: 40%; }
    .details td:last-child { font-weight: 600; }
    .footer { padding: 24px 40px; background: #F5F8FC; border-top: 1px solid #D2DCE6; }
    .footer p { font-size: 12px; color: #5A7A96; margin: 0; line-height: 1.6; }
    .footer a { color: #00A8F8; }
    .btn { display: inline-block; background: #00A8F8; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-size: 14px; font-weight: 700; margin: 16px 0; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><span>AUTO</span>TERRA <span class="badge-admin">Admin</span></h1>
    </div>
    <div class="body">
      @if ($type === 'created')
        <h2>New Subscription Created</h2>
        <p>A new subscription has been created by <strong>{{ $subscription->user->name }}</strong> ({{ $subscription->user->email }}).</p>
      @else
        <h2>Subscription Status Changed</h2>
        <p>Subscription <strong>#{{ $subscription->id }}</strong> by <strong>{{ $subscription->user->name }}</strong> has been updated.</p>
        <div class="status-update">
          <span class="old-status">{{ ucfirst($oldStatus) }}</span>
          <span class="arrow">→</span>
          <span class="new-status">{{ ucfirst($newStatus) }}</span>
        </div>
      @endif

      <div class="details">
        <table>
          <tr><td>Subscription ID</td><td>#{{ $subscription->id }}</td></tr>
          <tr><td>Customer</td><td>{{ $subscription->user->name }} ({{ $subscription->user->email }})</td></tr>
          <tr><td>Product</td><td>{{ $subscription->product->name }}</td></tr>
          <tr><td>Term</td><td>{{ termLabel($subscription->term) }}</td></tr>
          <tr><td>Amount</td><td>{{ $subscription->currency === 'INR' ? '₹' . number_format($subscription->amount, 0) : '$' . number_format($subscription->amount, 2) }} / {{ $subscription->term }}</td></tr>
          <tr><td>Status</td><td>{{ ucfirst($subscription->status) }}</td></tr>
          @if($subscription->current_period_end)
          <tr><td>Next billing date</td><td>{{ $subscription->current_period_end->format('M j, Y') }}</td></tr>
          @endif
          @if($subscription->razorpay_subscription_id)
          <tr><td>Razorpay ID</td><td style="font-size:11px;">{{ $subscription->razorpay_subscription_id }}</td></tr>
          @endif
        </table>
      </div>

      <a href="{{ config('app.url') }}/admin/subscriptions/{{ $subscription->id }}/edit" class="btn">View Subscription in Admin</a>
    </div>
    <div class="footer">
      <p>AutoTerra by Infyterra Technologies · <a href="{{ config('app.url') }}">autoterra.net</a></p>
    </div>
  </div>
</body>
</html>
