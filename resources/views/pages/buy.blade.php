@extends('layouts.app')
@section('title', 'Buy AutoTerra — Pricing & Plans')

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
.buy-layout{display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:start;margin-top:28px;}
.tier-label{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:20px 0 10px;display:flex;align-items:center;gap:8px;}
.tier-label::after{content:'';flex:1;height:1px;background:var(--border);}
.tier-label.basic{color:var(--muted);}
.tier-label.professional{color:var(--blue);}
.tier-label.advanced{color:var(--cyan);}
.buy-product-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:4px;}
.buy-card{border:2px solid var(--border);border-radius:12px;padding:18px;background:#fff;cursor:pointer;transition:border-color 0.2s,box-shadow 0.2s,transform 0.15s;position:relative;}
.buy-card:hover{border-color:var(--cyan);transform:translateY(-1px);}
.buy-card.selected{border-color:var(--cyan);box-shadow:0 0 0 2px rgba(0,168,248,0.20);background:var(--cyan-lt);}
.buy-card-badge{position:absolute;top:-1px;right:16px;background:var(--cyan);color:#fff;font-size:9px;font-weight:700;padding:3px 10px;border-radius:0 0 6px 6px;letter-spacing:0.5px;text-transform:uppercase;}
.buy-card-name{font-size:13px;font-weight:800;color:var(--body);margin-bottom:6px;}
.buy-card.selected .buy-card-name{color:var(--blue);}
.buy-card-price-wrap{margin-bottom:10px;}
.buy-card-per-mo{font-size:22px;font-weight:800;color:var(--body);letter-spacing:-0.5px;line-height:1;}
.buy-card.selected .buy-card-per-mo{color:var(--blue);}
.buy-card-per-mo-label{font-size:11px;color:var(--muted);font-weight:500;}
.buy-card-total{font-size:11px;color:var(--muted);margin-top:2px;}
.buy-card-saving{display:inline-block;font-size:10px;font-weight:700;background:var(--green-lt);color:#0B6A48;border-radius:4px;padding:2px 7px;margin-top:3px;}
.buy-card-na{font-size:12px;color:var(--muted);font-style:italic;}
.buy-card-features{border-top:1px solid var(--border);margin-top:10px;padding-top:10px;display:flex;flex-direction:column;gap:4px;}
.buy-card.selected .buy-card-features{border-top-color:rgba(0,168,248,0.20);}
.buy-card-feat{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px;line-height:1.4;}
.buy-card-feat i{font-size:12px;color:var(--cyan);flex-shrink:0;}
.buy-card-select-ring{display:none;position:absolute;top:12px;right:12px;width:18px;height:18px;border-radius:50%;background:var(--cyan);color:#fff;align-items:center;justify-content:center;font-size:11px;}
.buy-card.selected .buy-card-select-ring{display:flex;}
.buy-sidebar{position:sticky;top:72px;}
.order-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;}
.order-head{background:var(--navy);padding:14px 20px;}
.order-head h4{font-size:13px;font-weight:700;color:#fff;margin-bottom:2px;}
.order-head p{font-size:11px;color:rgba(210,230,248,0.40);}
.order-body{padding:18px 20px;}
.order-empty{text-align:center;padding:28px 0;color:var(--muted);font-size:13px;}
.order-empty i{font-size:32px;display:block;margin-bottom:8px;}
.order-item{display:none;}
.order-item.visible{display:block;}
.order-product-name{font-size:14px;font-weight:800;color:var(--body);margin-bottom:2px;}
.order-term-label{font-size:11px;color:var(--muted);margin-bottom:14px;}
.order-price-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;}
.order-price-label{font-size:12px;color:var(--muted);}
.order-price-val{font-size:12px;font-weight:600;color:var(--body);}
.order-divider{height:1px;background:var(--border);margin:12px 0;}
.order-total-row{display:flex;justify-content:space-between;align-items:baseline;}
.order-total-label{font-size:13px;font-weight:700;color:var(--body);}
.order-total-val{font-size:20px;font-weight:800;color:var(--body);letter-spacing:-0.3px;}
.order-saving-row{display:flex;justify-content:space-between;align-items:center;margin-top:6px;}
.order-saving-badge{font-size:11px;font-weight:700;background:var(--green-lt);color:#0B6A48;border-radius:4px;padding:3px 8px;}
.order-saving-amt{font-size:11px;color:var(--green);font-weight:700;}
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
.form-control-q{width:100%;padding:10px 13px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:13px;color:var(--body);background:#fff;outline:none;transition:border-color 0.15s;}
.form-control-q:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,168,248,0.10);}
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
          <h4>Your order</h4>
          <p>Select a product to continue</p>
        </div>
        <div class="order-body">
          <div class="order-empty" id="orderEmpty">
            <i class="ti ti-shopping-cart"></i>
            Select a plan from the left to see your order summary
          </div>
          <div class="order-item" id="orderItem">
            <div class="order-product-name" id="orderProductName"></div>
            <div class="order-term-label" id="orderTermLabel"></div>
            <div class="order-price-row">
              <span class="order-price-label">Per month (equiv.)</span>
              <span class="order-price-val" id="orderPerMo"></span>
            </div>
            <div class="order-price-row">
              <span class="order-price-label">Subtotal (excl. GST)</span>
              <span class="order-price-val" id="orderSubtotal"></span>
            </div>
            <div class="order-price-row">
              <span class="order-price-label">GST (18%)</span>
              <span class="order-price-val" id="orderGst"></span>
            </div>
            <div class="order-divider"></div>
            <div class="order-total-row">
              <span class="order-total-label">Total</span>
              <span class="order-total-val" id="orderTotal"></span>
            </div>
            <div class="order-saving-row" id="orderSavingRow" style="display:none;">
              <span class="order-saving-badge" id="orderSavingPct"></span>
              <span class="order-saving-amt" id="orderSavingAmt"></span>
            </div>
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
let PRICING = {};
let selectedProduct = null;
let selectedTerm = '1yr';
let billingMode = 'upfront';
let currency = 'INR';
let pendingSubscriptionId = null;
const ALL_TERMS = ['daily','weekly','3mo','6mo','1yr','3yr','5yr'];
const TERM_DAYS = {'daily':1,'weekly':7,'3mo':90,'6mo':180,'1yr':365,'3yr':1095,'5yr':1825};
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
  if (currency === 'INR') return '₹' + Number(n).toLocaleString('en-IN', {minimumFractionDigits: n % 1 === 0 ? 0 : 2, maximumFractionDigits: 2});
  return '$' + Number(n).toLocaleString('en-US', {minimumFractionDigits: n % 1 === 0 ? 0 : 2, maximumFractionDigits: 2});
}

function toPaise(amount) {
  return Math.round(amount * 100);
}

async function loadPricing() {
  try {
    const res = await fetch('/api/pricing?currency=' + currency);
    const data = await res.json();
    PRICING = data.products;
    renderAll();
  } catch(e) { console.error('Pricing load failed', e); }
}

function renderAll() {
  renderTermTabs();
  renderCards();
  if (selectedProduct) updateOrder();
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

function getPrice(slug) {
  const p = PRICING[slug];
  if (!p || !p.prices[selectedTerm]) return null;
  const v = p.prices[selectedTerm];
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
      html += `<div class="buy-card${slug === selectedProduct ? ' selected' : ''}${isPopular ? ' popular-card' : ''}" id="card-${slug}" onclick="selectProduct('${slug}')">
        ${isPopular ? '<div class="buy-card-badge hot">Most Popular</div>' : ''}
        <div class="buy-card-select-ring"><i class="ti ti-check" style="font-size:10px;"></i></div>
        <div class="buy-card-name">${product.name}</div>
        <div class="buy-card-price-wrap" id="price-${slug}">
          ${price != null ? renderPriceHTML(price) : '<div class="buy-card-na">Contact for pricing</div>'}
        </div>
        <div class="buy-card-features">
          ${features.map(f => `<div class="buy-card-feat"><i class="ti ti-check"></i> ${f}</div>`).join('')}
        </div>
      </div>`;
    });
    html += '</div>';
  });
  if (currency === 'INR') {
    html += `<div class="tax-note"><i class="ti ti-info-circle"></i> Prices are in Indian Rupees (₹) and <strong>exclusive of GST (18%)</strong>. GST will be added at checkout.</div>`;
  } else {
    html += `<div class="tax-note"><i class="ti ti-info-circle"></i> Prices are in USD. Tax will be calculated at checkout based on your location.</div>`;
  }
  col.innerHTML = html;
}

function renderPriceHTML(price) {
  const days = TERM_DAYS[selectedTerm];
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

function selectProduct(slug) {
  if (!PRICING[slug] || !PRICING[slug].prices[selectedTerm]) {
    const availableTerms = ALL_TERMS.filter(t => PRICING[slug]?.prices[t] != null);
    selectedTerm = availableTerms.length ? availableTerms[Math.min(2, availableTerms.length - 1)] : '1yr';
  }
  selectedProduct = slug;
  renderAll();
}

function setTerm(term) {
  selectedTerm = term;
  renderAll();
}

function setBillingMode(mode) {
  billingMode = mode;
  document.getElementById('tabUpfront').classList.toggle('active', mode === 'upfront');
  document.getElementById('tabMonthly').classList.toggle('active', mode === 'monthly');
  if (selectedProduct) updateOrder();
}

function updateOrder() {
  if (!selectedProduct || !PRICING[selectedProduct]) return;
  const price = getPrice(selectedProduct);
  if (price == null) return;
  const days = TERM_DAYS[selectedTerm];
  const gst = currency === 'INR' ? Math.round(price * GST_RATE) : 0;
  const total = price + gst;

  let perUnitLabel = '/mo';
  let perUnit = price;
  if (selectedTerm === 'daily') { perUnitLabel = '/day'; perUnit = price; }
  else if (selectedTerm === 'weekly') { perUnitLabel = '/week'; perUnit = price; }
  else {
    const months = Math.round(days / 30);
    perUnit = Math.round(price / months * 100) / 100;
  }

  const isSub = billingMode === 'monthly';

  document.getElementById('orderEmpty').style.display = 'none';
  document.getElementById('orderItem').classList.add('visible');
  document.getElementById('checkoutBtn').disabled = false;
  document.getElementById('orderProductName').textContent = PRICING[selectedProduct].name;
  document.getElementById('orderTermLabel').textContent = TERM_LABELS[selectedTerm] + (isSub ? ' subscription (recurring)' : ' one-time');
  document.getElementById('orderPerMo').textContent = fmt(perUnit) + perUnitLabel;
  document.getElementById('orderSubtotal').textContent = fmt(price);
  document.getElementById('orderGst').textContent = currency === 'INR' ? fmt(gst) : '—';
  document.getElementById('orderTotal').textContent = fmt(total);

  const btn = document.getElementById('checkoutBtn');
  if (isSub) {
    btn.innerHTML = '<i class="ti ti-refresh"></i> Subscribe now';
  } else {
    btn.innerHTML = '<i class="ti ti-lock"></i> Proceed to checkout';
  }
}

async function handleCheckout() {
  if (!selectedProduct) { alert('Please select a product first.'); return; }
  const price = getPrice(selectedProduct);
  if (!price) { alert('Price not available.'); return; }

  if (!IS_LOGGED_IN) {
    window.location.href = '/login?redirect=/buy';
    return;
  }

  const btn = document.getElementById('checkoutBtn');
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 0.8s linear infinite;"></i> Processing…';
  btn.disabled = true;

  if (billingMode === 'monthly') {
    await handleSubscriptionCheckout(price);
  } else {
    await handleOneTimeCheckout(price);
  }
}

async function handleOneTimeCheckout(price) {
  const gst = currency === 'INR' ? Math.round(price * GST_RATE) : 0;
  const totalAmount = price + gst;
  const amountForRazorpay = toPaise(totalAmount);

  try {
    const res = await fetch('/api/razorpay/create-order', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ product_slug: selectedProduct, term: selectedTerm, amount: amountForRazorpay, currency: currency })
    });
    const order = await res.json();
    if (order.error) { alert('Error: ' + order.error); resetBtn(); return; }

    const rzp = new Razorpay({
      key: RZP_KEY,
      amount: order.amount,
      currency: order.currency,
      name: 'AutoTerra',
      description: PRICING[selectedProduct].name + ' — ' + TERM_LABELS[selectedTerm],
      order_id: order.id,
      handler: async function(response) {
        const vRes = await fetch('/api/razorpay/verify', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({ ...response, db_order_id: order.db_order_id })
        });
        const vData = await vRes.json();
        if (vData.success) {
          window.location.href = '/dashboard?success=payment';
        } else {
          alert('Payment verification failed.'); resetBtn();
        }
      },
      modal: { ondismiss: () => { resetBtn(); } },
      prefill: { name: '{{ Auth::user()->name ?? "" }}', email: '{{ Auth::user()->email ?? "" }}' },
      theme: { color: '#00A8F8' }
    });
    rzp.open();
  } catch(e) {
    alert('Payment failed. Please try again.'); resetBtn();
  }
}

async function handleSubscriptionCheckout(price) {
  const gst = currency === 'INR' ? Math.round(price * GST_RATE) : 0;
  const totalAmount = price + gst;

  try {
    const res = await fetch('/api/razorpay/create-plan', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({
        product_slug: selectedProduct,
        term: selectedTerm,
        amount: totalAmount,
        currency: currency
      })
    });
    const data = await res.json();
    if (data.error) { alert('Error: ' + data.error); resetBtn(); return; }

    pendingSubscriptionId = data.subscription_id;

    const rzp = new Razorpay({
      key: RZP_KEY,
      subscription_id: data.subscription_id,
      name: 'AutoTerra',
      description: PRICING[selectedProduct].name + ' — ' + TERM_LABELS[selectedTerm] + ' subscription',
      handler: async function(response) {
        pendingSubscriptionId = null;
        const vRes = await fetch('/api/razorpay/verify-subscription', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({
            razorpay_subscription_id: response.razorpay_subscription_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature
          })
        });
        const vData = await vRes.json();
        if (vData.success) {
          window.location.href = '/dashboard?success=subscription';
        } else {
          alert('Subscription verification failed.'); resetBtn();
        }
      },
      modal: { ondismiss: () => { resetBtn(); } },
      prefill: { name: '{{ Auth::user()->name ?? "" }}', email: '{{ Auth::user()->email ?? "" }}' },
      theme: { color: '#00A8F8' }
    });
    rzp.open();
  } catch(e) {
    alert('Subscription failed. Please try again.'); resetBtn();
  }
}

function resetBtn() {
  if (pendingSubscriptionId) {
    fetch('/api/razorpay/cancel-pending-subscription', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ subscription_id: pendingSubscriptionId })
    }).catch(function() {});
    pendingSubscriptionId = null;
  }
  const btn = document.getElementById('checkoutBtn');
  if (billingMode === 'monthly') {
    btn.innerHTML = '<i class="ti ti-refresh"></i> Subscribe now';
  } else {
    btn.innerHTML = '<i class="ti ti-lock"></i> Proceed to checkout';
  }
  btn.disabled = false;
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
  const p = new URLSearchParams(window.location.search);
  if (p.get('product')) selectedProduct = p.get('product');
  detectGeo();
});

document.getElementById('menuToggle').addEventListener('click', function() {
  const links = document.querySelector('.nav-links');
  if (!links.style.display || links.style.display === 'none') {
    links.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:56px;left:0;right:0;background:var(--navy);padding:16px 24px;gap:14px;border-bottom:1px solid rgba(0,168,248,0.12);z-index:99;';
  } else { links.style.display = 'none'; }
});
</script>
@endsection
