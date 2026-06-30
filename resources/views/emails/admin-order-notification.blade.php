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
    .divider { height: 1px; background: #D2DCE6; margin: 16px 0; }
    .total-row td { font-size: 16px; font-weight: 800; color: #1C2B3A; padding-top: 12px; }
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
        <h2>New Order Received</h2>
        <p>A new order has been placed by <strong>{{ $order->user->name }}</strong> ({{ $order->user->email }}).</p>
      @else
        <h2>Order Status Changed</h2>
        <p>Order <strong>#{{ $order->id }}</strong> by <strong>{{ $order->user->name }}</strong> has been updated.</p>
        <div class="status-update">
          <span class="old-status">{{ ucfirst($oldStatus) }}</span>
          <span class="arrow">→</span>
          <span class="new-status">{{ ucfirst($newStatus) }}</span>
        </div>
      @endif

      <div class="details">
        <table>
          <tr><td>Order ID</td><td>#{{ $order->id }}</td></tr>
          <tr><td>Customer</td><td>{{ $order->user->name }} ({{ $order->user->email }})</td></tr>
          <tr><td>Product</td><td>{{ $order->product->name }}</td></tr>
          <tr><td>Term</td><td>{{ termLabel($order->term) }}</td></tr>
          <tr><td>Payment Method</td><td>{{ strtoupper($order->currency) }}</td></tr>
          <tr><td>Status</td><td>{{ ucfirst($order->status) }}</td></tr>
          @if($order->razorpay_payment_id)
          <tr><td>Payment ID</td><td>{{ $order->razorpay_payment_id }}</td></tr>
          @endif
          <tr class="divider"><td colspan="2"><div class="divider"></div></td></tr>
          <tr><td>Subtotal</td><td>{{ $order->currency === 'INR' ? '₹' . number_format($order->amount, 0) : '$' . number_format($order->amount, 2) }}</td></tr>
          @if($order->gst_amount > 0)
          <tr><td>GST (18%)</td><td>{{ '₹' . number_format($order->gst_amount, 0) }}</td></tr>
          @endif
          <tr class="total-row"><td>Total</td><td>{{ $order->currency === 'INR' ? '₹' . number_format($order->total_amount, 0) : '$' . number_format($order->total_amount, 2) }}</td></tr>
        </table>
      </div>

      <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}/edit" class="btn">View Order in Admin</a>
    </div>
    <div class="footer">
      <p>AutoTerra by Infyterra Technologies · <a href="{{ config('app.url') }}">autoterra.net</a></p>
    </div>
  </div>
</body>
</html>
