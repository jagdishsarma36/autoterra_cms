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
      <h1><span>AUTO</span>TERRA</h1>
    </div>
    <div class="body">
      <h2>Order Confirmed</h2>
      <p>Thank you for your purchase! Your order has been successfully processed.</p>

      <div class="details">
        <table>
          <tr><td>Order ID</td><td>#{{ $order->id }}</td></tr>
          <tr><td>Product</td><td>{{ $order->product->name }}</td></tr>
          <tr><td>Term</td><td>{{ termLabel($order->term) }}</td></tr>
          <tr><td>Payment Method</td><td>{{ strtoupper($order->currency) }}</td></tr>
          <tr><td>Status</td><td style="color:#1DA870;font-weight:700;">{{ ucfirst($order->status) }}</td></tr>
          @php
            $heading = match ($status) { 'paid' => 'Order Confirmed', 'refunded' => 'Refund Processed', ... };
            $message = match ($status) { 'paid' => 'Thank you for your purchase!...', 'refunded' => 'Your refund has been processed...', ... };
          @endphp
          @if($order->status === 'paid')
            <p>Your license key will be available...</p>
          @endif
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

      <a href="{{ config('app.url') }}/dashboard" class="btn">View in Dashboard</a>

      <p style="font-size:13px;">Your license key will be available in your dashboard. You can download and activate your software immediately.</p>
    </div>
    <div class="footer">
      <p>AutoTerra by Infyterra Technologies · <a href="{{ config('app.url') }}">autoterra.net</a></p>
      <p style="margin-top:8px;">Questions? Reply to this email or contact <a href="mailto:support@autoterra.net">support@autoterra.net</a></p>
    </div>
  </div>
</body>
</html>
