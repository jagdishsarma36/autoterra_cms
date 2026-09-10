<script>
/* Shared cart logic — requires these globals declared by the including page BEFORE this include:
   RZP_KEY, IS_LOGGED_IN, CSRF_TOKEN, billingMode, currency, pendingOrderId,
   pendingSubscriptionId, cartData, plus helpers fmt() and api().
   Also requires markup with IDs: cartEmpty, cartItems, couponSection, couponInput,
   couponApplied, couponCode, orderSummary, checkoutBtn, cartSubtitle, cartCount,
   cartSubtotal, cartGst, cartDiscountRow, cartDiscount, cartTotal. */
async function removeFromCart(slug, term) {
  const data = await api('/api/cart/remove', { product_slug: slug, term: term });
  if (data.contents) {
    cartData = { items: data.contents.items, subtotal: data.contents.subtotal, gst: data.contents.gst, discount: data.contents.discount, total: data.contents.total, coupon_code: data.contents.coupon_code, item_count: data.item_count };
  }
  renderCart();
}

async function updateQty(slug, term, qty) {
  if (qty <= 0) { removeFromCart(slug, term); return; }
  const data = await api('/api/cart/update', { product_slug: slug, term: term, quantity: qty });
  if (data.contents) {
    cartData = { items: data.contents.items, subtotal: data.contents.subtotal, gst: data.contents.gst, discount: data.contents.discount, total: data.contents.total, coupon_code: data.contents.coupon_code, item_count: data.item_count };
  }
  renderCart();
}

async function applyCoupon() {
  const code = document.getElementById('couponCode').value.trim();
  if (!code) return;

  const data = await api('/api/cart/coupon/apply', { code: code });
  if (data.error) { alert(data.error); return; }

  cartData.coupon_code = data.coupon_code;
  cartData.subtotal = data.contents.subtotal;
  cartData.gst = data.contents.gst;
  cartData.discount = data.contents.discount;
  cartData.total = data.contents.total;
  renderCart();
}

async function removeCoupon() {
  const data = await api('/api/cart/coupon/remove', {});
  if (data.contents) {
    cartData.coupon_code = null;
    cartData.subtotal = data.contents.subtotal;
    cartData.gst = data.contents.gst;
    cartData.discount = data.contents.discount;
    cartData.total = data.contents.total;
  }
  renderCart();
}

async function loadCart() {
  if (!IS_LOGGED_IN) return;
  try {
    const res = await fetch('/api/cart');
    const data = await res.json();
    cartData = data;
    renderCart();
  } catch(e) { console.error('Cart load failed', e); }
}

function renderCart() {
  const items = cartData?.items || [];
  const count = cartData?.item_count || 0;
  const hasItems = items.length > 0;

  document.getElementById('cartEmpty').style.display = hasItems ? 'none' : 'block';
  document.getElementById('cartItems').style.display = hasItems ? 'block' : 'none';
  document.getElementById('couponSection').style.display = hasItems ? 'block' : 'none';
  document.getElementById('orderSummary').style.display = hasItems ? 'block' : 'none';
  document.getElementById('checkoutBtn').disabled = !hasItems;
  document.getElementById('cartSubtitle').textContent = hasItems ? count + ' item' + (count !== 1 ? 's' : '') + ' in cart' : 'Add products to get started';

  const countBadge = document.getElementById('cartCount');
  if (count > 0) { countBadge.style.display = 'inline'; countBadge.textContent = count; }
  else { countBadge.style.display = 'none'; }

  let html = '';
  items.forEach(item => {
    html += `<div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${item.product_name}</div>
        <div class="cart-item-term">${item.term_label}</div>
        <div class="cart-item-price">${fmt(item.unit_price * item.quantity)}</div>
        <div class="cart-item-qty">
          <button onclick="updateQty('${item.product_slug}','${item.term}',${item.quantity - 1})">−</button>
          <span>${item.quantity}</span>
          <button onclick="updateQty('${item.product_slug}','${item.term}',${item.quantity + 1})">+</button>
        </div>
      </div>
      <button class="cart-item-remove" onclick="removeFromCart('${item.product_slug}','${item.term}')" title="Remove"><i class="ti ti-x"></i></button>
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

  document.getElementById('cartSubtotal').textContent = fmt(cartData?.subtotal || 0);
  document.getElementById('cartGst').textContent = fmt(cartData?.gst || 0);

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
  let badge = document.getElementById('navCartBadge');
  if (count > 0) {
    if (!badge) {
      badge = document.createElement('span');
      badge.id = 'navCartBadge';
      badge.style.cssText = 'background:var(--cyan);color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:4px;';
      const cartLink = document.querySelector('a[href="/buy"]');
      if (cartLink) cartLink.appendChild(badge);
    }
    badge.textContent = count;
  } else if (badge) {
    badge.remove();
  }
}

async function handleCheckout() {
  const items = cartData?.items || [];
  if (!items.length) { alert('Your cart is empty.'); return; }

  if (!IS_LOGGED_IN) { window.location.href = '/login?redirect=/cart'; return; }

  const btn = document.getElementById('checkoutBtn');
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 0.8s linear infinite;"></i> Processing…';
  btn.disabled = true;

  if (billingMode === 'monthly' && items.length === 1) {
    await handleSubscriptionCheckout(items[0]);
  } else {
    await handleCartCheckout();
  }
}

async function handleSubscriptionCheckout(item) {
  const totalAmount = cartData?.total || item.line_total;

  try {
    const data = await api('/api/razorpay/create-plan', {
      product_slug: item.product_slug,
      term: item.term,
      amount: totalAmount,
      currency: currency
    });
    if (data.error) { alert('Error: ' + data.error); resetBtn(); return; }

    pendingSubscriptionId = data.subscription_id;

    const rzp = new Razorpay({
      key: RZP_KEY,
      subscription_id: data.subscription_id,
      name: 'AutoTerra',
      description: item.product_name + ' — ' + item.term_label + ' subscription',
      handler: async function(response) {
        pendingSubscriptionId = null;
        const vData = await api('/api/razorpay/verify-subscription', {
          razorpay_subscription_id: response.razorpay_subscription_id,
          razorpay_payment_id: response.razorpay_payment_id,
          razorpay_signature: response.razorpay_signature
        });
        if (vData.success) {
          await api('/api/cart/remove', { product_slug: item.product_slug, term: item.term });
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

async function handleCartCheckout() {
  const totalPaise = Math.round((cartData?.total || 0) * 100);

  try {
    const data = await api('/api/razorpay/create-cart-order', {
      amount: totalPaise,
      currency: currency,
      coupon_code: cartData?.coupon_code || null
    });
    if (data.error) { alert('Error: ' + data.error); resetBtn(); return; }

    pendingOrderId = data.db_order_id;

    const rzp = new Razorpay({
      key: RZP_KEY,
      amount: data.amount,
      currency: data.currency,
      name: 'AutoTerra',
      description: 'AutoTerra — ' + (cartData?.item_count || 1) + ' product' + ((cartData?.item_count || 1) !== 1 ? 's' : ''),
      order_id: data.id,
      handler: async function(response) {
        pendingOrderId = null;
        const vData = await api('/api/razorpay/verify', {
          ...response,
          db_order_id: data.db_order_id
        });
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

function resetBtn() {
  if (pendingSubscriptionId) {
    api('/api/razorpay/cancel-pending-subscription', { subscription_id: pendingSubscriptionId }).catch(function() {});
    pendingSubscriptionId = null;
  }
  if (pendingOrderId) {
    api('/api/razorpay/cancel-pending-order', { order_id: pendingOrderId }).catch(function() {});
    pendingOrderId = null;
  }
  const btn = document.getElementById('checkoutBtn');
  btn.innerHTML = '<i class="ti ti-lock"></i> Proceed to checkout';
  btn.disabled = !(cartData?.items?.length > 0);
}
</script>