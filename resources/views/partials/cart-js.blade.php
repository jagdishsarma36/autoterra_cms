<script>
/* Shared cart logic — requires these globals declared by the including page BEFORE this include:
   RZP_KEY, IS_LOGGED_IN, CSRF_TOKEN, currency, cartData, plus helpers fmt() and api().
   The buy page additionally declares billingMode / selectedTerm for its product cards.
   Also requires markup with IDs: cartEmpty, cartItems, couponSection, couponInput,
   couponApplied, couponCode, orderSummary, checkoutBtn, cartSubtitle, cartCount,
   cartSubtotal, cartGst, cartDiscountRow, cartDiscount, cartTotal. */
const CART_MODE_UPFRONT = 'upfront';
const CART_MODE_SUBSCRIPTION = 'monthly';

let pendingOrderId = null;
let pendingSubscriptionIds = [];

function applyCartPayload(data) {
  if (!data) return;
  // Mutation endpoints nest the contents under `contents`; GET /api/cart
  // returns the same fields flat alongside `item_count`.
  const c = data.contents || data;
  if (!c || !c.items) return;
  cartData = {
    items: c.items,
    subtotal: c.subtotal,
    gst: c.gst,
    discount: c.discount,
    total: c.total,
    coupon_code: c.coupon_code,
    coupon_on_upfront_only: !!c.coupon_on_upfront_only,
    item_count: data.item_count ?? c.item_count ?? 0,
    billing_mode: c.billing_mode,
    has_subscriptions: !!c.has_subscriptions,
    has_upfront: !!c.has_upfront,
    is_mixed: !!c.is_mixed,
  };
}

async function removeFromCart(slug, term, mode) {
  const data = await api('/api/cart/remove', { product_slug: slug, term: term, billing_mode: mode || null });
  applyCartPayload(data);
  renderCart();
}

async function updateQty(slug, term, qty, mode) {
  if (qty <= 0) { removeFromCart(slug, term, mode); return; }
  const data = await api('/api/cart/update', { product_slug: slug, term: term, quantity: qty, billing_mode: mode || null });
  applyCartPayload(data);
  renderCart();
}

async function applyCoupon() {
  const code = document.getElementById('couponCode').value.trim();
  if (!code) return;

  const data = await api('/api/cart/coupon/apply', { code: code });
  if (data.error) { alert(data.error); return; }

  applyCartPayload(data);
  renderCart();
}

async function removeCoupon() {
  const data = await api('/api/cart/coupon/remove', {});
  applyCartPayload(data);
  renderCart();
}

async function loadCart() {
  if (!IS_LOGGED_IN) return;
  try {
    const res = await fetch('/api/cart');
    const data = await res.json();
    applyCartPayload(data);
    renderCart();
  } catch(e) { console.error('Cart load failed', e); }
}

function renderCart() {
  const items = cartData?.items || [];
  const count = cartData?.item_count || 0;
  const hasItems = items.length > 0;
  // Coupons discount a one-time payment, so they are hidden once the cart
  // contains a subscription line.
  const subsOnly = hasItems && !cartData?.has_upfront;

  document.getElementById('cartEmpty').style.display = hasItems ? 'none' : 'block';
  document.getElementById('cartItems').style.display = hasItems ? 'block' : 'none';
  document.getElementById('couponSection').style.display = (hasItems && !subsOnly) ? 'block' : 'none';
  document.getElementById('orderSummary').style.display = hasItems ? 'block' : 'none';
  document.getElementById('checkoutBtn').disabled = !hasItems;
  document.getElementById('cartSubtitle').textContent = hasItems ? count + ' item' + (count !== 1 ? 's' : '') + ' in cart' : 'Add products to get started';

  const subNote = document.getElementById('cartSubNote');
  if (subNote) {
    subNote.style.display = cartData?.has_subscriptions ? 'block' : 'none';
  }

  const countBadge = document.getElementById('cartCount');
  if (count > 0) { countBadge.style.display = 'inline'; countBadge.textContent = count; }
  else { countBadge.style.display = 'none'; }

  let html = '';
  items.forEach(item => {
    const isSub = !!item.is_subscription;
    const qty = isSub
      ? '<div class="cart-item-qty"><span class="cart-item-fixed"><i class="ti ti-repeat"></i> Billed every cycle</span></div>'
      : `<div class="cart-item-qty">
          <button onclick="updateQty('${item.product_slug}','${item.term}',${item.quantity - 1},'${CART_MODE_UPFRONT}')">−</button>
          <span>${item.quantity}</span>
          <button onclick="updateQty('${item.product_slug}','${item.term}',${item.quantity + 1},'${CART_MODE_UPFRONT}')">+</button>
        </div>`;
    const price = isSub
      ? `${fmt(item.cycle_amount)} <span class="cart-item-term">/ cycle</span>`
      : fmt(item.unit_price * item.quantity);
    const modeBadge = isSub
      ? '<span class="cart-item-badge">Subscription</span>'
      : '<span class="cart-item-badge cart-item-badge-once">One-time</span>';

    html += `<div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${item.product_name} ${modeBadge}</div>
        <div class="cart-item-term">${item.term_label}</div>
        <div class="cart-item-price">${price}</div>
        ${qty}
      </div>
      <button class="cart-item-remove" onclick="removeFromCart('${item.product_slug}','${item.term}','${isSub ? CART_MODE_SUBSCRIPTION : CART_MODE_UPFRONT}')" title="Remove"><i class="ti ti-x"></i></button>
    </div>`;
  });
  document.getElementById('cartItems').innerHTML = html;

  const couponSection = document.getElementById('couponInput');
  const appliedSection = document.getElementById('couponApplied');
  if (cartData?.coupon_code) {
    couponSection.style.display = 'none';
    appliedSection.style.display = 'flex';
    appliedSection.innerHTML = `<span><i class="ti ti-ticket"></i> ${cartData.coupon_code}</span><button onclick="removeCoupon()" title="Remove coupon"><i class="ti ti-x"></i></button>`;
  } else {
    couponSection.style.display = 'flex';
    appliedSection.style.display = 'none';
  }

  // A coupon only discounts the one-time lines, so say so rather than letting
  // the customer assume the subscription was discounted too.
  const couponSubNote = document.getElementById('couponSubNote');
  if (couponSubNote) {
    couponSubNote.style.display = cartData?.coupon_on_upfront_only ? 'block' : 'none';
  }

  document.getElementById('cartSubtotal').textContent = fmt(cartData?.subtotal || 0);
  document.getElementById('cartGst').textContent = fmt(cartData?.gst || 0);

  // A subscription line is charged one cycle now, so the summary shows the
  // amount due today rather than the whole term.
  const subLabel = document.getElementById('cartSubtotalLabel');
  if (subLabel) subLabel.textContent = cartData?.has_subscriptions ? 'Due now (excl. GST)' : 'Subtotal';
  const totalLabel = document.getElementById('cartTotalLabel');
  if (totalLabel) totalLabel.textContent = cartData?.has_subscriptions ? 'Payable now' : 'Total';

  const discountRow = document.getElementById('cartDiscountRow');
  if ((cartData?.discount || 0) > 0) {
    discountRow.style.display = 'flex';
    document.getElementById('cartDiscount').textContent = '−' + fmt(cartData.discount);
  } else {
    discountRow.style.display = 'none';
  }

  document.getElementById('cartTotal').textContent = fmt(cartData?.total || 0);

  updateNavCartBadge(count);
}

function updateNavCartBadge(count) {
  const badge = document.getElementById('navCartBadge');
  if (!badge) return;
  if (count > 0) {
    badge.textContent = count;
    badge.style.display = '';
  } else {
    badge.style.display = 'none';
  }
}

async function handleCheckout() {
  const items = cartData?.items || [];
  if (!items.length) { alert('Your cart is empty.'); return; }

  if (!IS_LOGGED_IN) { window.location.href = '/login?redirect=/cart'; return; }

  // Subscription lines are settled by their own Razorpay plan, one checkout
  // per line. One-time lines are settled last, in a single combined order.
  const subs = items.filter(i => i.is_subscription);
  const upfront = items.filter(i => !i.is_subscription);

  setBusy(true, subs.length > 1 ? 'Starting subscription 1 of ' + subs.length + '…' : 'Processing…');

  let completed = true;
  let subsAttempted = 0;
  let subsConfirmed = 0;

  for (const item of subs) {
    subsAttempted++;
    if (subs.length > 1) setBusy(true, 'Subscription ' + subsAttempted + ' of ' + subs.length + '…');
    const ok = await checkoutSubscriptionLine(item);
    if (!ok) { completed = false; break; }
    subsConfirmed++;
    await dropCartLine(item, CART_MODE_SUBSCRIPTION);
  }

  if (completed && upfront.length) {
    setBusy(true, 'Processing payment…');
    completed = await checkoutUpfrontLines(upfront);
  }

  if (completed) {
    window.location.href = subs.length ? '/dashboard?success=subscription' : '/dashboard?success=payment';
    return;
  }

  await reloadCart();
  setBusy(false);
  alert(subsConfirmed > 0
    ? subsConfirmed + ' of ' + subs.length + ' subscriptions confirmed and are now active. Please finish the remaining items in your cart.'
    : 'Payment was not completed. Your cart has been saved.');
}

/**
 * Create a plan + subscription for one cart line and open Razorpay for it.
 * Resolves true once the first payment is verified.
 */
function checkoutSubscriptionLine(item) {
  return new Promise(async (resolve) => {
    let data;
    try {
      data = await api('/api/razorpay/create-plan', {
        product_slug: item.product_slug,
        term: item.term
      });
    } catch (e) {
      alert('Subscription failed. Please try again.');
      return resolve(false);
    }

    if (data.error) { alert('Error: ' + data.error); return resolve(false); }

    pendingSubscriptionIds.push(data.subscription_id);

    const rzp = new Razorpay({
      key: RZP_KEY,
      subscription_id: data.subscription_id,
      name: 'AutoTerra',
      description: item.product_name + ' — ' + item.term_label + ' subscription',
      handler: async function(response) {
        const vData = await api('/api/razorpay/verify-subscription', {
          razorpay_subscription_id: response.razorpay_subscription_id,
          razorpay_payment_id: response.razorpay_payment_id,
          razorpay_signature: response.razorpay_signature
        });
        if (vData && vData.success) {
          dropPendingSubscription(response.razorpay_subscription_id);
          resolve(true);
        } else {
          cancelPendingSubscriptions();
          alert('Subscription verification failed.');
          resolve(false);
        }
      },
      modal: { ondismiss: () => { cancelPendingSubscriptions(); resolve(false); } },
      prefill: { name: '{{ Auth::user()->name ?? "" }}', email: '{{ Auth::user()->email ?? "" }}' },
      theme: { color: '#00A8F8' }
    });
    rzp.open();
  });
}

function checkoutUpfrontLines(items) {
  return new Promise(async (resolve) => {
    let data;
    try {
      data = await api('/api/razorpay/create-cart-order', {
        currency: currency,
        coupon_code: cartData?.coupon_code || null,
        items: items.map(i => ({ product_slug: i.product_slug, term: i.term }))
      });
    } catch (e) {
      alert('Payment failed. Please try again.');
      return resolve(false);
    }

    if (data.error) { alert('Error: ' + data.error); return resolve(false); }

    pendingOrderId = data.db_order_id;

    const rzp = new Razorpay({
      key: RZP_KEY,
      amount: data.amount,
      currency: data.currency,
      name: 'AutoTerra',
      description: 'AutoTerra — ' + items.length + ' product' + (items.length !== 1 ? 's' : ''),
      order_id: data.id,
      handler: async function(response) {
        const vData = await api('/api/razorpay/verify', {
          ...response,
          db_order_id: data.db_order_id
        });
        pendingOrderId = null;
        if (vData && vData.success) {
          resolve(true);
        } else {
          alert('Payment verification failed.');
          resolve(false);
        }
      },
      modal: { ondismiss: () => { cancelPendingOrder(); resolve(false); } },
      prefill: { name: '{{ Auth::user()->name ?? "" }}', email: '{{ Auth::user()->email ?? "" }}' },
      theme: { color: '#00A8F8' }
    });
    rzp.open();
  });
}

/** Remove a purchased line and re-render, keeping checkout loops in control. */
async function dropCartLine(item, mode) {
  const data = await api('/api/cart/remove', { product_slug: item.product_slug, term: item.term, billing_mode: mode });
  applyCartPayload(data);
  renderCart();
}

function dropPendingSubscription(razorpaySubscriptionId) {
  pendingSubscriptionIds = pendingSubscriptionIds.filter(id => id !== razorpaySubscriptionId);
}

function cancelPendingSubscriptions() {
  const ids = pendingSubscriptionIds.slice();
  pendingSubscriptionIds = [];
  ids.forEach(id => {
    api('/api/razorpay/cancel-pending-subscription', { subscription_id: id }).catch(function() {});
  });
}

function cancelPendingOrder() {
  if (!pendingOrderId) return;
  const id = pendingOrderId;
  pendingOrderId = null;
  api('/api/razorpay/cancel-pending-order', { order_id: id }).catch(function() {});
}

async function reloadCart() {
  try {
    const res = await fetch('/api/cart');
    applyCartPayload(await res.json());
    renderCart();
  } catch (e) { /* keep the current view if the refresh fails */ }
}

function setBusy(busy, label) {
  const btn = document.getElementById('checkoutBtn');
  if (!btn) return;
  btn.innerHTML = busy
    ? '<i class="ti ti-loader-2" style="animation:spin 0.8s linear infinite;"></i> ' + (label || 'Processing…')
    : '<i class="ti ti-lock"></i> Proceed to checkout';
  btn.disabled = busy || !(cartData?.items?.length > 0);
}
</script>