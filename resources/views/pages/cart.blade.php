@extends('layouts.app')
@section('title', 'Your Cart — AutoTerra')
@section('meta_description', 'Review your AutoTerra selections and complete checkout securely.')

@section('styles')
<style>
.cart-hero{background:var(--navy);padding:36px 60px 30px;border-bottom:1px solid rgba(0,168,248,0.10);}
.cart-hero h1{font-size:26px;font-weight:800;color:#fff;letter-spacing:-0.5px;margin-bottom:6px;}
.cart-hero h1 span{color:var(--cyan);}
.cart-hero p{font-size:13px;color:rgba(210,230,248,0.45);}
.cart-layout{display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start;padding:32px 60px 60px;}
.cart-panel{background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden;}
.cart-panel-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.cart-panel-head h4{font-size:13px;font-weight:800;color:var(--body);margin:0;}
.cart-panel-head .cart-count{background:var(--cyan);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;}
.cart-panel-head p{font-size:11px;color:var(--muted);margin:2px 0 0;}
.cart-item{display:flex;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border);align-items:flex-start;}
.cart-item:last-child{border-bottom:none;}
.cart-item-info{flex:1;min-width:0;}
.cart-item-name{font-size:13px;font-weight:700;color:var(--body);}
.cart-item-term{font-size:11px;color:var(--muted);margin-top:1px;}
.cart-item-price{font-size:12px;font-weight:600;color:var(--body);margin-top:3px;}
.cart-item-qty{display:flex;align-items:center;gap:6px;margin-top:6px;}
.cart-item-qty button{width:24px;height:24px;border:1px solid var(--border);border-radius:5px;background:#fff;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--body);transition:border-color 0.15s,color 0.15s;}
.cart-item-qty button:hover{border-color:var(--cyan);color:var(--cyan);}
.cart-item-qty span{font-size:12px;font-weight:600;min-width:18px;text-align:center;}
.cart-item-remove{background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;font-size:16px;flex-shrink:0;}
.cart-item-remove:hover{color:#EF4444;}
.cart-empty{text-align:center;padding:56px 20px;color:var(--muted);font-size:13px;}
.cart-empty i{font-size:40px;display:block;margin-bottom:10px;color:var(--border);}
.cart-empty a{display:inline-block;margin-top:16px;padding:10px 20px;background:var(--navy);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;}
.cart-empty a:hover{background:var(--cyan);}
.order-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;position:sticky;top:72px;}
.order-head{background:var(--navy);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;}
.order-head h4{font-size:13px;font-weight:700;color:#fff;margin:0;}
.order-head p{font-size:11px;color:rgba(210,230,248,0.40);margin:0;}
.cart-coupon{padding:16px 20px;border-bottom:1px solid var(--border);}
.cart-coupon label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;}
.cart-coupon-row{display:flex;gap:6px;}
.cart-coupon-row input{flex:1;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-family:inherit;font-size:12px;color:var(--body);outline:none;}
.cart-coupon-row input:focus{border-color:var(--cyan);}
.cart-coupon-row button{padding:8px 14px;background:var(--navy);color:#fff;border:none;border-radius:6px;font-family:inherit;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;}
.cart-coupon-row button:hover{background:var(--cyan);}
.cart-coupon-applied{display:flex;align-items:center;justify-content:space-between;background:#F0FFF8;border:1px solid #6EE7B7;border-radius:6px;padding:6px 10px;font-size:11px;color:#065F46;font-weight:600;}
.cart-coupon-applied button{background:none;border:none;color:#991B1B;cursor:pointer;font-size:13px;padding:0 2px;}
.order-summary{padding:16px 20px;}
.order-price-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;}
.order-price-label{font-size:12px;color:var(--muted);}
.order-price-val{font-size:12px;font-weight:600;color:var(--body);}
.order-discount-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;color:#065F46;}
.order-discount-row .order-price-val{color:#065F46;}
.order-divider{height:1px;background:var(--border);margin:12px 0;}
.order-total-row{display:flex;justify-content:space-between;align-items:baseline;}
.order-total-label{font-size:13px;font-weight:700;color:var(--body);}
.order-total-val{font-size:20px;font-weight:800;color:var(--body);letter-spacing:-0.3px;}
.order-cta{padding:0 20px 16px;}
.btn-buy{width:100%;padding:13px 0;background:var(--cyan);color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.2s;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;}
.btn-buy:hover{background:var(--cyan-dk);}
.btn-buy:disabled{background:var(--border);color:var(--muted);cursor:default;}
.btn-buy-ghost{width:100%;padding:10px 0;background:transparent;color:var(--muted);border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.15s;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;}
.btn-buy-ghost:hover{border-color:var(--cyan);color:var(--cyan);}
.order-security{padding:12px 20px;border-top:1px solid var(--border);display:flex;gap:12px;font-size:11px;color:var(--muted);flex-wrap:wrap;}
.order-security span{display:flex;align-items:center;gap:4px;}
.order-security i{font-size:13px;color:var(--green);}
.order-help{padding:14px 20px;border-top:1px solid var(--border);background:var(--off);}
.order-help p{font-size:12px;color:var(--muted);line-height:1.55;}
.order-help a{color:var(--cyan);font-weight:700;}
@media(max-width:900px){.cart-hero,.cart-layout{padding-left:24px;padding-right:24px;}.cart-layout{grid-template-columns:1fr;padding-top:24px;}.order-card{position:static;}}
</style>
@endsection

@section('body')
@php $rup = fn($n) => '₹' . number_format((float) $n, 2, '.', ','); @endphp
@include('partials.nav')

<div class="cart-hero">
  <h1>Your <span>Cart</span></h1>
  <p id="cartSubtitle">{{ $item_count ? $item_count . ' item' . ($item_count !== 1 ? 's' : '') . ' in cart' : 'Add products to get started' }}</p>
</div>

<div class="cart-layout">
  <div class="cart-panel">
    <div class="cart-panel-head">
      <div>
        <h4>Selected Products</h4>
        <p>Review quantities and coupon before checkout</p>
      </div>
      <span class="cart-count" id="cartCount" style="display:none;">0</span>
    </div>

    <div id="cartEmpty" class="cart-empty" style="{{ $item_count ? 'display:none;' : '' }}">
      <i class="ti ti-shopping-cart-off"></i>
      Your cart is empty.
      <br><a href="/buy"><i class="ti ti-shopping-cart-plus"></i> Browse products</a>
    </div>

    <div id="cartItems" style="{{ $item_count ? '' : 'display:none;' }}">
      @if($item_count)
        @foreach($contents['items'] as $item)
        <div class="cart-item">
          <div class="cart-item-info">
            <div class="cart-item-name">{{ $item['product_name'] }}</div>
            <div class="cart-item-term">{{ $item['term_label'] }}</div>
            <div class="cart-item-price">{{ $rup($item['unit_price'] * $item['quantity']) }}</div>
            <div class="cart-item-qty">
              <button onclick="updateQty('{{ $item['product_slug'] }}','{{ $item['term'] }}',{{ $item['quantity'] - 1 }})">−</button>
              <span>{{ $item['quantity'] }}</span>
              <button onclick="updateQty('{{ $item['product_slug'] }}','{{ $item['term'] }}',{{ $item['quantity'] + 1 }})">+</button>
            </div>
          </div>
          <button class="cart-item-remove" onclick="removeFromCart('{{ $item['product_slug'] }}','{{ $item['term'] }}')" title="Remove"><i class="ti ti-x"></i></button>
        </div>
        @endforeach
      @endif
    </div>
  </div>

  <div class="order-card">
    <div class="order-head">
      <h4>Order Summary</h4>
    </div>

    <div id="couponSection" class="cart-coupon" style="{{ $item_count ? '' : 'display:none;' }}">
      <div id="couponInput" class="cart-coupon-row" style="{{ $coupon_code ? 'display:none;' : '' }}">
        <input type="text" id="couponCode" placeholder="Coupon code">
        <button onclick="applyCoupon()">Apply</button>
      </div>
      <div id="couponApplied" class="cart-coupon-applied" style="{{ $coupon_code ? '' : 'display:none;' }}">
        <span><i class="ti ti-ticket"></i> {{ $coupon_code }}</span>
        <button onclick="removeCoupon()" title="Remove coupon"><i class="ti ti-x"></i></button>
      </div>
    </div>

    <div id="orderSummary" class="order-summary" style="{{ $item_count ? '' : 'display:none;' }}">
      <div class="order-price-row"><span class="order-price-label">Subtotal</span><span class="order-price-val" id="cartSubtotal">{{ $rup($contents['subtotal']) }}</span></div>
      <div class="order-discount-row" id="cartDiscountRow" style="{{ $contents['discount'] > 0 ? '' : 'display:none;' }}"><span class="order-price-label">Discount</span><span class="order-price-val" id="cartDiscount">−{{ $rup($contents['discount']) }}</span></div>
      <div class="order-price-row"><span class="order-price-label">GST (18%)</span><span class="order-price-val" id="cartGst">{{ $rup($contents['gst']) }}</span></div>
      <div class="order-divider"></div>
      <div class="order-total-row"><span class="order-total-label">Total</span><span class="order-total-val" id="cartTotal">{{ $rup($contents['total']) }}</span></div>
    </div>

    <div class="order-cta">
      <button class="btn-buy" id="checkoutBtn" onclick="handleCheckout()" {{ $item_count ? '' : 'disabled' }}><i class="ti ti-lock"></i> Proceed to checkout</button>
      <a class="btn-buy-ghost" href="/buy"><i class="ti ti-arrow-left"></i> Continue shopping</a>
    </div>

    <div class="order-security">
      <span><i class="ti ti-shield-check"></i> Secure payment via Razorpay</span>
      <span><i class="ti ti-refresh"></i> Instant license delivery</span>
    </div>
    <div class="order-help">
      <p>Need help? <a href="/contact">Contact us</a> or get a <a href="/quote">custom quote</a>.</p>
    </div>
  </div>
</div>

<script>
const menuToggle = document.getElementById('menuToggle');
if (menuToggle) {
  menuToggle.addEventListener('click', function() {
    const links = document.querySelector('.nav-links');
    if (!links.style.display || links.style.display === 'none') {
      links.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:56px;left:0;right:0;background:var(--navy);padding:16px 24px;gap:14px;border-bottom:1px solid rgba(0,168,248,0.12);z-index:99;';
    } else { links.style.display = 'none'; }
  });
}
</script>
@endsection

@section('scripts')
<script>
const RZP_KEY = '{{ config("razorpay.key_id") }}';
const IS_LOGGED_IN = true;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let billingMode = 'upfront';
let currency = 'INR';
let pendingOrderId = null;
let pendingSubscriptionId = null;
let cartData = null;

function fmt(n) {
  if (n == null) return '—';
  return '₹' + Number(n).toLocaleString('en-IN', {minimumFractionDigits: n % 1 === 0 ? 0 : 2, maximumFractionDigits: 2});
}

async function api(url, data) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
    body: JSON.stringify(data)
  });
  return res.json();
}

window.addEventListener('DOMContentLoaded', function() {
  loadCart();
});
</script>
@include('partials.cart-js')
@endsection