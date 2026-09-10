@extends('layouts.app')
@section('title', 'Buy AutoTerra — Pricing & Plans')
@section('meta_description', 'Buy AutoTerra geospatial software for LiDAR processing, terrain modeling, and GIS workflows. Choose the right solution for your mapping needs.')

@section('styles')
<style>
.geo-banner{padding:10px 60px;font-size:13px;display:flex;align-items:center;gap:10px;}
.geo-banner.detecting{background:var(--off);color:var(--muted);}
.geo-banner.india{background:#E8F5E9;color:#1B5E20;border-bottom:1px solid #A5D6A7;}
.geo-banner.intl{background:var(--amber-lt);color:#7A4A00;border-bottom:1px solid #F0C97A;}
.buy-hero{background:var(--navy);padding:44px 60px 36px;border-bottom:1px solid rgba(0,168,248,0.10);}
.buy-hero h1{font-size:30px;font-weight:800;color:#fff;letter-spacing:-0.5px;margin-bottom:8px;}
.buy-hero h1 span{color:var(--cyan);}
.buy-hero p{font-size:14px;color:rgba(210,230,248,0.45);}
.intl-section{display:none;padding:48px 60px;}
.intl-notice{background:var(--amber-lt);border:1px solid #F0C97A;border-radius:10px;padding:20px 24px;display:flex;gap:14px;align-items:flex-start;margin-bottom:36px;}
.intl-notice i{font-size:22px;color:var(--amber);flex-shrink:0;margin-top:2px;}
.intl-notice h4{font-size:15px;font-weight:700;color:var(--body);margin-bottom:4px;}
.intl-notice p{font-size:13px;color:var(--muted);line-height:1.6;}
.india-section{display:none;padding:0 60px 60px;}
.billing-mode-wrap{padding:24px 0 0;display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
.billing-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;}
.billing-tabs{display:flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;}
.billing-tab{padding:8px 18px;font-size:13px;font-weight:600;background:#fff;color:var(--muted);border:none;cursor:pointer;transition:background 0.15s,color 0.15s;}
.billing-tab.active{background:var(--navy);color:#fff;}
.term-wrap{margin:20px 0 0;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.term-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
.term-tabs{display:flex;gap:6px;flex-wrap:wrap;}
.term-tab{padding:8px 16px;border-radius:20px;font-size:12px;font-weight:700;border:1px solid var(--border);background:#fff;color:var(--muted);cursor:pointer;transition:all 0.15s;display:flex;align-items:center;gap:6px;}
.term-tab:hover{border-color:var(--cyan);color:var(--body);}
.term-tab.active{background:var(--navy);color:#fff;border-color:var(--navy);}
.term-tab.term-disabled{opacity:0.35;cursor:not-allowed;pointer-events:none;text-decoration:line-through;}
.term-tab .badge-pop{background:var(--cyan);color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:20px;letter-spacing:0.3px;}
.term-tab .badge-val{background:var(--green);color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:20px;}
.buy-layout{display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start;margin-top:28px;}
.tier-label{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:20px 0 10px;display:flex;align-items:center;gap:8px;}
.tier-label::after{content:'';flex:1;height:1px;background:var(--border);}
.tier-label.basic{color:var(--muted);}
.tier-label.professional{color:var(--blue);}
.tier-label.advanced{color:var(--cyan);}
.buy-product-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:4px;}
.buy-card{border:2px solid var(--border);border-radius:12px;padding:18px;background:#fff;transition:border-color 0.2s,box-shadow 0.2s,transform 0.15s;position:relative;}
.buy-card:hover{border-color:var(--cyan);transform:translateY(-1px);}
.buy-card-badge{position:absolute;top:-1px;right:16px;background:var(--cyan);color:#fff;font-size:9px;font-weight:700;padding:3px 10px;border-radius:0 0 6px 6px;letter-spacing:0.5px;text-transform:uppercase;}
.buy-card-name{font-size:13px;font-weight:800;color:var(--body);margin-bottom:6px;}
.buy-card-price-wrap{margin-bottom:10px;}
.buy-card-per-mo{font-size:22px;font-weight:800;color:var(--body);letter-spacing:-0.5px;line-height:1;}
.buy-card-per-mo-label{font-size:11px;color:var(--muted);font-weight:500;}
.buy-card-total{font-size:11px;color:var(--muted);margin-top:2px;}
.buy-card-na{font-size:12px;color:var(--muted);font-style:italic;}
.buy-card-features{border-top:1px solid var(--border);margin-top:10px;padding-top:10px;display:flex;flex-direction:column;gap:4px;}
.buy-card-feat{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px;line-height:1.4;}
.buy-card-feat i{font-size:12px;color:var(--cyan);flex-shrink:0;}
.buy-card-actions{display:flex;gap:6px;margin-top:12px;}
.btn-add-cart{flex:1;padding:8px 0;background:var(--navy);color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:11px;font-weight:700;cursor:pointer;transition:background 0.2s;display:flex;align-items:center;justify-content:center;gap:5px;}
.btn-add-cart:hover{background:var(--cyan);}
.btn-add-cart.added{background:var(--green);pointer-events:none;}
.buy-sidebar{position:sticky;top:72px;}
.order-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;}
.order-head{background:var(--navy);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;}
.order-head h4{font-size:13px;font-weight:700;color:#fff;margin-bottom:0;}
.order-head .cart-count{background:var(--cyan);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;}
.order-head p{font-size:11px;color:rgba(210,230,248,0.40);margin:0;}
.order-body{padding:18px 20px;max-height:400px;overflow-y:auto;}
.order-empty{text-align:center;padding:28px 0;color:var(--muted);font-size:13px;}
.order-empty i{font-size:32px;display:block;margin-bottom:8px;}
.cart-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);align-items:flex-start;}
.cart-item:last-child{border-bottom:none;}
.cart-item-info{flex:1;min-width:0;}
.cart-item-name{font-size:12px;font-weight:700;color:var(--body);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.cart-item-term{font-size:10px;color:var(--muted);margin-top:1px;}
.cart-item-price{font-size:11px;font-weight:600;color:var(--body);margin-top:3px;}
.cart-item-qty{display:flex;align-items:center;gap:4px;margin-top:4px;}
.cart-item-qty button{width:20px;height:20px;border:1px solid var(--border);border-radius:4px;background:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--body);}
.cart-item-qty button:hover{border-color:var(--cyan);color:var(--cyan);}
.cart-item-qty span{font-size:11px;font-weight:600;min-width:16px;text-align:center;}
.cart-item-remove{background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;font-size:14px;flex-shrink:0;}
.cart-item-remove:hover{color:#EF4444;}
.cart-divider{height:1px;background:var(--border);margin:12px 0;}
.cart-coupon{padding:0 20px 14px;}
.cart-coupon label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;}
.cart-coupon-row{display:flex;gap:6px;}
.cart-coupon-row input{flex:1;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-family:inherit;font-size:12px;color:var(--body);outline:none;}
.cart-coupon-row input:focus{border-color:var(--cyan);}
.cart-coupon-row button{padding:8px 14px;background:var(--navy);color:#fff;border:none;border-radius:6px;font-family:inherit;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;}
.cart-coupon-row button:hover{background:var(--cyan);}
.cart-coupon-applied{display:flex;align-items:center;justify-content:space-between;background:#F0FFF8;border:1px solid #6EE7B7;border-radius:6px;padding:6px 10px;font-size:11px;color:#065F46;font-weight:600;}
.cart-coupon-applied button{background:none;border:none;color:#991B1B;cursor:pointer;font-size:13px;padding:0 2px;}
.order-summary{padding:0 20px 18px;}
.order-price-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;}
.order-price-label{font-size:12px;color:var(--muted);}
.order-price-val{font-size:12px;font-weight:600;color:var(--body);}
.order-discount-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;color:#065F46;}
.order-discount-row .order-price-val{color:#065F46;}
.order-divider{height:1px;background:var(--border);margin:12px 0;}
.order-total-row{display:flex;justify-content:space-between;align-items:baseline;}
.order-total-label{font-size:13px;font-weight:700;color:var(--body);}
.order-total-val{font-size:20px;font-weight:800;color:var(--body);letter-spacing:-0.3px;}
.order-cta{padding:0 20px 20px;}
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
.tax-note{font-size:11px;color:var(--muted);margin-top:20px;padding:10px 14px;background:var(--off);border-radius:6px;border:1px solid var(--border);}
.tax-note i{color:var(--cyan);font-size:13px;vertical-align:-2px;}
@media(max-width:900px){.geo-banner,.buy-hero,.india-section,.intl-section{padding-left:24px;padding-right:24px;}.buy-layout{grid-template-columns:1fr;}.buy-sidebar{position:static;}.buy-product-grid{grid-template-columns:1fr;}}
</style>
@endsection

@section('body')
@include('partials.nav')

<div class="geo-banner detecting" id="geoBanner">
  <i class="ti ti-loader-2" style="animation:spin 0.8s linear infinite;"></i>
  Detecting your location…
</div>

<section class="buy-hero">
  <div class="sec-eye">Plans & Pricing</div>
  <h1>Buy <span>AutoTerra</span></h1>
  <p>All plans include updates during the subscription period and standard email support. Node-locked license — one activation per seat.</p>
</section>

<div class="india-section" id="indiaSection">
  <div class="billing-mode-wrap">
    <span class="billing-label">Billing:</span>
    <div class="billing-tabs">
      <button class="billing-tab active" id="tabUpfront" onclick="setBillingMode('upfront')">Pay upfront</button>
      <button class="billing-tab" id="tabMonthly" onclick="setBillingMode('monthly')">Subscription (recurring)</button>
    </div>
  </div>

  <div class="term-wrap" id="termWrap">
    <span class="term-label">Term:</span>
    <div class="term-tabs" id="termTabs"></div>
  </div>

  <div class="buy-layout">
    <div class="buy-cards-col" id="buyCardsCol"></div>
    <div class="buy-sidebar">
      <div class="order-card">
        <div class="order-head">
          <div><h4>Shopping Cart</h4><p id="cartSubtitle">Add products to get started</p></div>
          <span class="cart-count" id="cartCount" style="display:none;">0</span>
        </div>
        <div class="order-body" id="cartBody">
          <div class="order-empty" id="cartEmpty">
            <i class="ti ti-shopping-cart"></i>
            Your cart is empty.<br>Add products from the left.
          </div>
          <div id="cartItems"></div>
        </div>
        <div class="cart-coupon" id="couponSection" style="display:none;">
          <label>Coupon Code</label>
          <div id="couponInput" class="cart-coupon-row">
            <input type="text" id="couponCode" placeholder="Enter code" maxlength="50">
            <button onclick="applyCoupon()">Apply</button>
          </div>
          <div id="couponApplied" style="display:none;"></div>
        </div>
        <div class="order-summary" id="orderSummary" style="display:none;">
          <div class="order-price-row">
            <span class="order-price-label">Subtotal (excl. GST)</span>
            <span class="order-price-val" id="cartSubtotal"></span>
          </div>
          <div class="order-price-row">
            <span class="order-price-label">GST (18%)</span>
            <span class="order-price-val" id="cartGst"></span>
          </div>
          <div class="order-discount-row" id="cartDiscountRow" style="display:none;">
            <span class="order-price-label">Discount</span>
            <span class="order-price-val" id="cartDiscount"></span>
          </div>
          <div class="order-divider"></div>
          <div class="order-total-row">
            <span class="order-total-label">Total</span>
            <span class="order-total-val" id="cartTotal"></span>
          </div>
        </div>
        <div class="order-cta">
          <button class="btn-buy" id="checkoutBtn" disabled onclick="handleCheckout()">
            <i class="ti ti-lock"></i> Proceed to checkout
          </button>
          <a href="/quote" class="btn-buy-ghost">
            <i class="ti ti-file-text"></i> Need a formal quote instead?
          </a>
        </div>
        <div class="order-security">
          <span><i class="ti ti-shield-check"></i> Secure payment</span>
          <span><i class="ti ti-refresh"></i> Cancel anytime</span>
          <span><i class="ti ti-headset"></i> Support included</span>
        </div>
        <div class="order-help">
          <p>Need floating license, multiple seats, or academic pricing? <a href="/quote">Request a formal quote →</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="intl-section" id="intlSection">
  <div class="intl-notice">
    <i class="ti ti-map-pin-off"></i>
    <div>
      <h4>Online purchase is currently available for India only</h4>
      <p>We detected your location as <strong id="intlCountry">outside India</strong>. Online checkout with INR pricing is available for Indian customers. For your region, our team will provide a customised quote.</p>
    </div>
  </div>
  <a href="/quote" class="btn-buy" style="max-width:300px;"><i class="ti ti-file-text"></i> Request a quote for your region</a>
</div>

@include('partials.footer')
@endsection

@section('scripts')
<script>
const RZP_KEY = '{{ config("razorpay.key_id") }}';
const IS_LOGGED_IN = {{ Auth::check() ? 'true' : 'false' }};
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let PRICING = {};
let selectedTerm = '1yr';
let billingMode = 'upfront';
let currency = 'INR';
let pendingOrderId = null;
let pendingSubscriptionId = null;
let cartData = null;
const ALL_TERMS = ['daily','weekly','3mo','6mo','1yr','3yr','5yr'];
const TERM_LABELS = {'daily':'Daily','weekly':'Weekly','3mo':'3-Month','6mo':'6-Month','1yr':'1-Year','3yr':'3-Year','5yr':'5-Year'};
const GST_RATE = 0.18;

const PRODUCT_FEATURES = {
  view: ['Point cloud 3D viewer','DXF / KML / SHP viewing','Basic measurements'],
  lt: ['Survey data editing','Basic contours & topo map','DXF export'],
  std: ['Full DTM / DSM generation','Cross-sections & volumes','Survey & COGO tools'],
  spatial: ['All Standard features','LiDAR point cloud processing','Rule-based classification'],
  pro: ['Full survey & CAD toolkit','Advanced terrain & DTM','Python scripting API'],
  prospatial: ['Complete platform','AI LiDAR classification (GPU)','Corridor & road module'],
};

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

async function loadPricing() {
  try {
    const res = await fetch('/api/pricing?currency=' + currency);
    const data = await res.json();
    PRICING = data.products;
    renderAll();
    loadCart();
  } catch(e) { console.error('Pricing load failed', e); }
}

function renderAll() {
  renderTermTabs();
  renderCards();
}

function renderTermTabs() {
  const wrap = document.getElementById('termTabs');
  wrap.innerHTML = '';
  ALL_TERMS.forEach(t => {
    const hasProduct = Object.values(PRICING).some(p => p.prices[t] != null);
    if (!hasProduct) return;
    const btn = document.createElement('button');
    btn.className = 'term-tab' + (t === selectedTerm ? ' active' : '');
    btn.dataset.term = t;
    btn.innerHTML = TERM_LABELS[t] + (t === '1yr' ? ' <span class="badge-pop">Popular</span>' : '') + (t === '3yr' ? ' <span class="badge-val">Best value</span>' : '');
    btn.onclick = function() { setTerm(t); };
    wrap.appendChild(btn);
  });
  const active = wrap.querySelector('.term-tab.active');
  if (!active && wrap.children.length > 0) {
    selectedTerm = wrap.children[0].dataset.term;
    wrap.children[0].classList.add('active');
  }
}

function getPrice(slug, term) {
  term = term || selectedTerm;
  const p = PRICING[slug];
  if (!p || !p.prices[term]) return null;
  const v = p.prices[term];
  return typeof v === 'object' ? v.amount : v;
}

function renderCards() {
  const col = document.getElementById('buyCardsCol');
  const tiers = {basic:'Basic', pro:'Professional', advanced:'Advanced'};
  let html = '';
  Object.entries(tiers).forEach(([tier, label]) => {
    const tierProducts = Object.entries(PRICING).filter(([k,v]) => v.tier === tier);
    if (!tierProducts.length) return;
    html += `<div class="tier-label ${tier === 'basic' ? 'basic' : tier === 'pro' ? 'professional' : 'advanced'}">${label}</div>`;
    html += '<div class="buy-product-grid">';
    tierProducts.forEach(([slug, product]) => {
      const price = getPrice(slug);
      const isPopular = slug === 'prospatial';
      const features = PRODUCT_FEATURES[slug] || [];
      const inCart = isItemInCart(slug, selectedTerm);
      html += `<div class="buy-card${isPopular ? ' popular-card' : ''}" id="card-${slug}">
        ${isPopular ? '<div class="buy-card-badge hot">Most Popular</div>' : ''}
        <div class="buy-card-name">${product.name}</div>
        <div class="buy-card-price-wrap" id="price-${slug}">
          ${price != null ? renderPriceHTML(price, slug) : '<div class="buy-card-na">Contact for pricing</div>'}
        </div>
        <div class="buy-card-features">
          ${features.map(f => `<div class="buy-card-feat"><i class="ti ti-check"></i> ${f}</div>`).join('')}
        </div>
        ${price != null ? `<div class="buy-card-actions">
          <button class="btn-add-cart${inCart ? ' added' : ''}" id="addBtn-${slug}" onclick="event.stopPropagation();addToCart('${slug}')">
            <i class="ti ti-${inCart ? 'check' : 'shopping-cart-plus'}"></i> ${inCart ? 'Added' : 'Add to Cart'}
          </button>
        </div>` : ''}
      </div>`;
    });
    html += '</div>';
  });
  html += `<div class="tax-note"><i class="ti ti-info-circle"></i> Prices are in Indian Rupees (₹) and <strong>exclusive of GST (18%)</strong>. GST will be added at checkout. Add multiple products to purchase together.</div>`;
  col.innerHTML = html;
}

function renderPriceHTML(price, slug) {
  const days = {'daily':1,'weekly':7,'3mo':90,'6mo':180,'1yr':365,'3yr':1095,'5yr':1825}[selectedTerm] || 365;
  if (selectedTerm === 'daily') {
    return `<div class="buy-card-per-mo">${fmt(price)}<span class="buy-card-per-mo-label">/day</span></div>
      <div class="buy-card-total">${fmt(price * 30)} est. monthly</div>`;
  }
  if (selectedTerm === 'weekly') {
    return `<div class="buy-card-per-mo">${fmt(price)}<span class="buy-card-per-mo-label">/week</span></div>
      <div class="buy-card-total">${fmt(price * 4.3)} est. monthly</div>`;
  }
  const months = Math.round(days / 30);
  const perMo = Math.round(price / months * 100) / 100;
  return `<div class="buy-card-per-mo">${fmt(perMo)}<span class="buy-card-per-mo-label">/mo</span></div>
    <div class="buy-card-total">${fmt(price)} total for ${TERM_LABELS[selectedTerm].toLowerCase()}</div>`;
}

function setTerm(term) {
  selectedTerm = term;
  renderAll();
}

function setBillingMode(mode) {
  billingMode = mode;
  document.getElementById('tabUpfront').classList.toggle('active', mode === 'upfront');
  document.getElementById('tabMonthly').classList.toggle('active', mode === 'monthly');
}

function isItemInCart(slug, term) {
  if (!cartData || !cartData.items) return false;
  return cartData.items.some(i => i.product_slug === slug && i.term === term);
}

async function addToCart(slug) {
  if (!IS_LOGGED_IN) { window.location.href = '/login?redirect=/buy'; return; }

  const price = getPrice(slug);
  if (!price) { alert('Price not available for this term.'); return; }

  const btn = document.getElementById('addBtn-' + slug);
  if (btn) { btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 0.8s linear infinite;"></i>'; btn.disabled = true; }

  const data = await api('/api/cart/add', { product_slug: slug, term: selectedTerm, quantity: 1 });
  if (data.error) { alert(data.error); if (btn) { btn.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Add to Cart'; btn.disabled = false; } return; }

  cartData = { items: data.contents.items, subtotal: data.contents.subtotal, gst: data.contents.gst, discount: data.contents.discount, total: data.contents.total, coupon_code: data.contents.coupon_code, item_count: data.item_count };
  renderCart();
  renderCards();
}

function detectGeo() {
  fetch('https://ipapi.co/json/')
    .then(r => r.json())
    .then(d => {
      const banner = document.getElementById('geoBanner');
      if (d.country_code === 'IN') {
        currency = 'INR';
        banner.className = 'geo-banner india';
        banner.innerHTML = '<i class="ti ti-circle-check"></i> India detected — INR pricing with GST. <a href="/quote">Change region?</a>';
        document.getElementById('indiaSection').style.display = 'block';
        document.getElementById('intlSection').style.display = 'none';
      } else {
        currency = 'USD';
        banner.className = 'geo-banner intl';
        banner.innerHTML = '<i class="ti ti-map-pin"></i> Showing USD pricing for <strong>' + (d.country_name || 'your region') + '</strong>. <a href="/quote">Get a local quote</a>';
        document.getElementById('intlSection').style.display = 'block';
        document.getElementById('indiaSection').style.display = 'none';
        document.getElementById('intlCountry').textContent = d.country_name || 'outside India';
      }
      loadPricing();
    })
    .catch(() => {
      currency = 'INR';
      document.getElementById('geoBanner').className = 'geo-banner india';
      document.getElementById('geoBanner').innerHTML = '<i class="ti ti-circle-check"></i> Default: INR pricing.';
      document.getElementById('indiaSection').style.display = 'block';
      loadPricing();
    });
}

window.addEventListener('DOMContentLoaded', function() {
  detectGeo();
});

document.getElementById('menuToggle').addEventListener('click', function() {
  const links = document.querySelector('.nav-links');
  if (!links.style.display || links.style.display === 'none') {
    links.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:56px;left:0;right:0;background:var(--navy);padding:16px 24px;gap:14px;border-bottom:1px solid rgba(0,168,248,0.12);z-index:99;';
  } else { links.style.display = 'none'; }
});
</script>
@include('partials.cart-js')
@endsection
