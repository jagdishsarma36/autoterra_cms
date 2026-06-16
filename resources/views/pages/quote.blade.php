@extends('layouts.app')
@section('title', 'Request a Quote — AutoTerra')
@section('body')
@include('partials.nav')

@php
  $form = App\Models\FormCms::where('slug', 'quote-form')->where('is_active', true)->first();
  $fields = $form ? $form->fields()->orderBy('sort_order')->get() : collect();
@endphp

<!-- Hero Section -->
@foreach(pageContentJson('quote', 'quote.hero_section') as $hero)
<section class="qt-hero">
  <div class="qt-hero-inner">
    <div class="qt-hero-badge">
      <span class="ti {{ $hero['icon'] }}"></span>
      {{ $hero['text'] }}
    </div>
    <h1>{!! $hero['title'] !!}</h1>
    <p>{{ $hero['description'] }}</p>
    <div class="qt-trust">
      @foreach($hero['trust_items'] as $item)
        <div class="qt-trust-item">
          <span class="ti {{ $item['icon'] }}"></span>
          {{ $item['text'] }}
        </div>
      @endforeach
    </div>
  </div>
</section>
@endforeach

<!-- qoute all secton -->
 <div class="qt-body">
  <div class="qt-form-col">
    <div class="qt-sec-head">
      <div class="qt-sec-num">1</div>
      <div class="qt-sec-label">Which edition are you interested in?</div>
    </div>
    @php
    $products = pageContentJson('quote', 'quote.products');
    $firstProduct = reset($products);
    @endphp

    <div class="product-grid" id="productGrid">
        @foreach($products as $id => $product)
            <label
                class="prod-card"
                data-product="{{ $id }}"
                onclick="selectProduct(this)">
                <input
                    type="radio"
                    name="product"
                    value="{{ $id }}"
                    {{ $loop->first ? 'checked' : '' }}
                >
                <div class="prod-card-check"></div>

                <div class="prod-card-name">
                    {{ $product['name'] ?? '' }}
                </div>

                <div class="prod-card-track">
                    {{ $product['track'] ?? '' }}
                </div>
                @if(!empty($product['badge']))
                <div class="prod-card-badge">
                  {{ $product['badge'] ?? '' }}
                </div>
                @endif

                <input type="hidden" name="product_name" value="{{ $product['name'] ?? '' }}">
            </label>
        @endforeach
    </div>

    <!-- Feature preview -->
    <div class="feature-compare" id="featureCompare">
        <div class="fc-header">
            <span class="ti ti-table"></span>
            <span class="fc-header-title" id="fcTitle">
                {{ $firstProduct['name'] ?? '' }} Features
            </span>
        </div>

        <div class="fc-row">
            <div class="fc-feature">Feature</div>
            <div class="fc-col-head">Included</div>
        </div>

        <div id="fcRows">
            @foreach($firstProduct['features'] ?? [] as $feature)
                <div class="fc-row">
                    <div class="fc-feature">
                        {{ $feature['name'] }}
                    </div>

                    @if(($feature['val'] ?? '') === 'yes')
                        <div class="fc-val yes">
                            <span class="ti ti-circle-check"></span> Yes
                        </div>
                    @elseif(($feature['val'] ?? '') === 'partial')
                        <div class="fc-val">
                            <span class="ti ti-clock"></span>
                            {{ $feature['note'] ?? 'Partial' }}
                        </div>
                    @else
                        <div class="fc-val">
                            <span class="ti ti-circle-x" style="color:var(--border)"></span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
        <hr class="qt-sec-divider">
       
        <!-- STEP 2: Term preference -->
        <div class="qt-sec-head">
          <div class="qt-sec-num">2</div>
          <div class="qt-sec-label">Preferred subscription term</div>
        </div>
        <p class="qt-sec-des">
          Longer terms attract significant savings. Let us know your preference and we'll highlight the best deal in your quote.
        </p>
        @php
          $products = pageContentJson('quote', 'quote.term_avail');
          $product = 'view'; 
          $terms = $products[$product] ?? [];
          $labels = [
              '3mo'    => '3 Months',
              '6mo'    => '6 Months',
              '1yr'    => '1 Year',
              '3yr'    => '3 Years',
              '5yr'    => '5 Years',
              'unsure' => 'Unsure',
          ];
          $tags = [
              '1yr' => 'SAVE',
              '3yr' => 'SAVE MORE',
              '5yr' => 'BEST VALUE',
              ];

          $first = true;
        @endphp

      <div class="term-pills" id="termPills">
      @foreach($terms as $term => $enabled)
          @php
              $selected = $first;
              $tag = $tags[$term] ?? null;
          @endphp

          <label
              class="term-pill
                  {{ (!$enabled && in_array($term, ['3mo', '6mo'])) ? 'term-pill-disabled' : '' }}
                  {{ $selected ? 'selected' : '' }}"
              {{ $enabled ? '' : '' }}
              onclick=selectTerm(this)
          >
              <input
                  type="radio"
                  name="term"
                  value="{{ $term }}"
                  {{ $selected ? 'checked' : '' }}
                  {{ !$enabled ? 'disabled' : '' }}
              >

              {{ $labels[$term] ?? $term }}

              @if($enabled && $tag)
                  <span class="save-tag">{{ $tag }}</span>
              @endif
          </label>

          @php $first = false; @endphp
          @endforeach
        </div>
        
        {{--<div class="term-pills" id="termPills">
          @foreach($quote['quote.term_avail']['terms'] as $term)
        <label
            class="term-pill {{ !empty($term['selected']) ? 'selected' : '' }}"
            onclick="selectTerm(this)"
        >
            <input
                type="radio"
                name="term"
                value="{{ $term['value'] }}"
                {{ !empty($term['selected']) ? 'checked' : '' }}
            >
            {{ $term['label'] }}
            @if(!empty($term['tag']))
                <span class="save-tag">
                    {{ $term['tag'] }}
                </span>
            @endif
        </label>
        @endforeach
      </div>--}}

    <hr class="qt-sec-divider">

    <!-- STEP 3: Licence type & seats -->
    <div class="qt-sec-head">
      <div class="qt-sec-num">3</div>
      <div class="qt-sec-label">Licence model &amp; team size</div>
    </div>

    @foreach(pageContentJson('quote', 'quote.license_section') as $license)
    <div class="license-toggle" id="licenseToggle">
      @foreach($license['licenses'] as $item)
      <label class="license-opt {{ !empty($item['selected']) ? 'selected' : '' }}" onclick="selectLicense(this)">
        <input 
            type="radio" 
            name="license" 
            value="{{ $item['value'] }}"
            {{ !empty($item['selected']) ? 'checked' : '' }}
        >
        <div class="license-opt-name">
            {{ $item['name'] }}
        </div>
        <div class="license-opt-desc">
            {{ $item['description'] }}
        </div>
      </label>
      @endforeach
    </div>
    <p class="field-hint">{{ $license['hint'] }}</p>

    <div class="form-row">
      <div class="form-group">
        <label>
        {{ $license['seat']['label'] }}
            @if(!empty($license['seat']['required']))
                <span class="req">*</span>
            @endif
        </label>
        <div class="seat-counter">
          <button type="button" class="seat-btn" onclick="changeSeat(-1)">−</button>
            <input 
                type="number" 
                id="seatInput" 
                class="seat-input"
                value="{{ $license['seat']['default'] }}"
                min="{{ $license['seat']['min'] }}"
                max="{{ $license['seat']['max'] }}"
            >
            <button type="button" class="seat-btn" onclick="changeSeat(1)">+</button>
        </div>
        <span class="field-hint">
            {{ $license['seat']['hint'] }}
        </span>
      </div>
      <div class="form-group">
        <label>
            {{ $license['deployment']['label'] }}
        </label>
        <select id="deploySelect">
            <option value="">
                {{ $license['deployment']['placeholder'] }}
            </option>
            @foreach($license['deployment']['options'] as $option)
            <option value="{{ $option['value'] }}">
                {{ $option['label'] }}
            </option>
            @endforeach
        </select>
      </div>
    </div>
    @endforeach

    <hr class="qt-sec-divider">

    <!-- STEP 4: Organisation details -->
    <div class="qt-sec-head">
      <div class="qt-sec-num">4</div>
      <div class="qt-sec-label">Your organisation</div>
    </div>
    <div class="qoute_form_wrap">
      <div class="form-row-q" id="quoteform">
        <p class="ct-form-title">{{ $form->name }}</p>
        @if($form->description)
          <p class="ct-form-sub"> {{ $form->description }} </p>
        @endif
          @if(session('form_success'))
      <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
      <i class="ti ti-check-circle" style="font-size:20px;color:#065F46;"></i>
      <span style="font-size:14px;color:#065F46;font-weight:600;">{{ session('form_success') }}</span>
      </div>
    @endif
    @if($errors->any())
    <div style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:8px;padding:16px 20px;    margin-bottom:24px;">
      <div style="font-size:14px;color:#B91C1C;font-weight:600;margin-bottom:8px;">Please fix the following errors:</div>
      @foreach($errors->all() as $error)
      <div style="font-size:13px;color:#B91C1C;">• {{ $error }}</div>
      @endforeach
      </div>
      @endif
      <form method="POST" action="{{ route('form.submit', $form->slug) }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="form_{{ $form->slug }}">
          @foreach($fields as $field)
          @php $span = $field->width == 100 || $field->width == 1 ? 'grid-column: 1 / -1;' : ''; @endphp
          <div style="{{ $span }}" class="field_width_{{ $field->width }} field_wrap_{{ $field->name }}" data-name="{{ $field->name }}" data-type="{{ $field->type }}">
            @if($field->type === 'hidden')
              <input type="hidden" name="field_{{ $field->name }}" value="{{ $field->placeholder }}">
            @else
              <label style="display:block;font-size:13px;font-weight:600;color:var(--body);margin-bottom:5px;">
                {{ $field->label }} @if($field->is_required)<span style="color:#EF4444;">*</span>@endif
              </label>
            @endif
            @if($field->type === 'text' || $field->type === 'email' || $field->type === 'number' || $field->type === 'phone' || $field->type === 'date' || $field->type === 'time')
              <input type="{{ $field->type === 'phone' ? 'tel' : $field->type }}"
                name="field_{{ $field->name }}"
                placeholder="{{ $field->placeholder }}"
                value="{{ old('field_' . $field->name) }}"
                @if($field->is_required) required @endif
                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;outline:none;">
            @endif
            @if($field->type === 'textarea')
              <textarea name="field_{{ $field->name }}"
                placeholder="{{ $field->placeholder }}"
                @if($field->is_required) required @endif
                rows="4"
                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;resize:vertical;outline:none;">{{ old('field_' . $field->name) }}</textarea>
            @endif
            @if($field->type === 'select')
              <select name="field_{{ $field->name }}"
                @if($field->is_required) required @endif
                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);background:#fff;box-sizing:border-box;">
                <option value="">{{ $field->placeholder ?: 'Select...' }}</option>
                @foreach($field->options as $option)
                <option value="{{ $option }}" {{ old('field_' . $field->name) === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
              </select>
            @endif
            @if($field->type === 'radio')
              <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
                @foreach($field->options as $option)
                <label class="radio-label field_lbl_{{ str_replace(' ', '_', $option) }}" style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
                  <input type="radio" name="field_{{ $field->name }}" value="{{ $option }}"
                    {{ old('field_' . $field->name) === $option ? 'checked' : '' }}
                    @if($field->is_required) required @endif
                    style="accent-color:var(--cyan);">
                  {{ $option }}
                </label>
                @endforeach
              </div>
            @endif
            @if($field->type === 'checkbox')
              <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
                @foreach($field->options as $option)
                <label class="checkbox-label field_lbl_{{ str_replace(' ', '_', $option) }}" style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
                  <input type="checkbox" name="field_{{ $field->name }}[]" value="{{ $option }}"
                    {{ in_array($option, old('field_' . $field->name, [])) ? 'checked' : '' }}
                    style="accent-color:var(--cyan);">
                  {{ $option }}
                </label>
                @endforeach
              </div>
            @endif
            @if($field->type === 'file')
              <input type="file" name="field_{{ $field->name }}"
                @if($field->is_required) required @endif
                style="width:100%;padding:8px;border:1px solid var(--border);border-radius:7px;font-size:13px;">
            @endif
            @if($field->help_text)
            <p class="help_text" style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $field->help_text }}</p>
            @endif
          </div>
          @endforeach
        </div>
        <div class="qt-submit-row" style="margin-top:24px;">
          <button class="qt-submit-btn" type="submit"
          >
            {{ $form->submit_button_text }}
          </button>
          <p class="qt-submit-note">Our sales team responds within <strong>1 business day</strong>.<br>No spam, no pressure.</p>   
        </div>
      </form>     
    </div>
    </div>
    
  </div><!-- /qt-form-col -->

  <!-- ── Sidebar ── -->
  @foreach(pageContentJson('quote', 'quote.sidebar') as $sidebar)
  <aside class="qt-sidebar">
      <div class="whats-next">
          <h3>{{ $sidebar['whats_next']['title'] }}</h3>
          @foreach($sidebar['whats_next']['steps'] as $step)
          <div class="wn-step">
              <div class="wn-icon">
                  <span class="ti {{ $step['icon'] }}"></span>
              </div>
              <div class="wn-text">
                  <h4>{{ $step['title'] }}</h4>
                  <p>{{ $step['description'] }}</p>
              </div>
          </div>
          @endforeach
      </div>
      <hr class="sidebar-rule">
      <div class="contact-card">
          <h4>{{ $sidebar['contact']['title'] }}</h4>
          @foreach($sidebar['contact']['items'] as $contact)
          <div class="contact-item">
              <span class="ti {{ $contact['icon'] }}"></span>
              @if($contact['type'] == 'link')
                  <a href="{{ $contact['url'] }}">
                      {{ $contact['value'] }}
                  </a>
              @else
                  <span>{{ $contact['value'] }}</span>
              @endif
          </div>
          @endforeach
      </div>
      <hr class="sidebar-rule">
      <ul class="assurance-list">
          @foreach($sidebar['assurances'] as $item)
          <li>
              <span class="ti {{ $item['icon'] }}"></span>
              {{ $item['text'] }}
          </li>
          @endforeach
      </ul>
      <hr class="sidebar-rule">
      <div class="region-note" style="margin-bottom:0;">
          <span class="ti {{ $sidebar['region_note']['icon'] }}"></span>
          <span>
              <strong>{{ $sidebar['region_note']['title'] }}</strong>
              {{ $sidebar['region_note']['text'] }}

              <a href="{{ $sidebar['region_note']['link_url'] }}"
                style="color:var(--amber);text-decoration:underline;font-weight:700;">
                  {{ $sidebar['region_note']['link_text'] }}
              </a>.
          </span>
      </div>
  </aside>
  @endforeach

</div>

@include('partials.footer')
@endsection

<script>
  const PRODUCTS = @json(pageContentJson('quote', 'quote.products'));
  const TERM_AVAIL = @json(pageContentJson('quote', 'quote.term_avail'));

    const TERM_LABELS = {
    '3mo': '3 Months',
    '6mo': '6 Months',
    '1yr': '1 Year',
    '3yr': '3 Years',
    '5yr': '5 Years',
    'unsure': 'Not Sure'
    };
    const TERM_TAGS = {
    '1yr': 'SAVE',
    '3yr': 'SAVE MORE',
    '5yr': 'BEST VALUE'
    };
    // const tag = TERM_TAGS[term];

let selectedProduct = 'prospatial';
let selectedTerm    = '1yr';
let selectedLicense = 'node';

/* ── Render feature table ── */
function renderFeatures(key) {
  const p = PRODUCTS[key];
  document.getElementById('fcTitle').textContent = p.name + ' — key capabilities';
  const tbody = document.getElementById('fcRows');
  tbody.innerHTML = p.features.map(f => {
    let valHtml;
    if (f.val === 'yes')    valHtml = '<div class="fc-val yes"><span class="ti ti-circle-check"></span> Yes</div>';
    else if (f.val === 'no') valHtml = '<div class="fc-val"><span class="ti ti-circle-x" style="color:var(--border)"></span></div>';
    else                     valHtml = `<div class="fc-val partial">${f.note || 'Partial'}</div>`;
    return `<div class="fc-row"><div class="fc-feature">${f.name}</div>${valHtml}</div>`;
  }).join('');
}

function updateTermPills(key) {
  const avail = TERM_AVAIL[key];
  let termSwitched = false;
  document.querySelectorAll('.term-pill').forEach(pill => {
    const t = pill.querySelector('input').value;
    if (avail[t] === false) {
      pill.classList.add('term-pill-disabled');
      pill.title = 'Not available for this edition (minimum 1 year)';
      if (pill.classList.contains('selected')) {
        pill.classList.remove('selected');
        termSwitched = true;
      }
    } else {
      pill.classList.remove('term-pill-disabled');
      pill.title = '';
    }
  });
  // Default to 1yr if current term was disabled
  if (termSwitched) {
    const t1yr = document.querySelector('.term-pill input[value="1yr"]');
    if (t1yr) {
      selectedTerm = '1yr';
      t1yr.closest('.term-pill').classList.add('selected');
      t1yr.checked = true;
    }
  }
}

/* ── Product selection ── */
function selectProduct(el) {
  document.querySelectorAll('.prod-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  selectedProduct = el.dataset.product;
  el.querySelector('input').checked = true;
  const productName = el.querySelector('[name="product_name"]')?.value || '';
  const editionField = document.querySelector(
      '.field_wrap_edition_type input[type="hidden"][name="field_edition_type"]'
  );

  if (editionField) {
        editionField.value = productName;
        editionField.dispatchEvent(new Event('change', { bubbles: true }));
    }
  renderFeatures(selectedProduct);
  updateTermPills(selectedProduct);
}

/* ── Term selection ── */
function selectTerm(el) {
  document.querySelectorAll('.term-pill').forEach(p => p.classList.remove('selected'));
  el.classList.add('selected');
  selectedTerm = el.querySelector('input').value;
  el.querySelector('input').checked = true;
  const hiddenField = document.querySelector(
        '.field_wrap_subscription_term input[type="hidden"][name="field_subscription_term"]'
    );

    if (hiddenField) {
        hiddenField.value = selectedTerm;
        hiddenField.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

/* ── License selection ── */
function selectLicense(el) {
  document.querySelectorAll('.license-opt').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  selectedLicense = el.querySelector('input').value;
  el.querySelector('input').checked = true;
  const hiddenField = document.querySelector(
        '.field_wrap_licence_model_eam input[type="hidden"][name="field_licence_model_eam"]'
    );
    if (hiddenField) {
        hiddenField.value = selectedLicense;
        hiddenField.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

/* ── Seat counter ── */
function changeSeat(delta) {
  const inp = document.getElementById('seatInput');
  let v = parseInt(inp.value) || 1;
  v = Math.max(1, Math.min(999, v + delta));
  inp.value = v;
   const seatValue = inp.value;
    const hiddenField = document.querySelector(
        '.field_wrap_number_seat input[type="hidden"][name="field_number_seat"]'
    );

    if (hiddenField) {
        hiddenField.value = seatValue;
    }
}

/* ── Deployment ── */
document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'deploySelect') {
        const selectedValue = e.target.value;

        const hiddenField = document.querySelector(
            '.field_wrap_deployment input[name="field_deployment"]'
        );

        if (hiddenField) {
            hiddenField.value = selectedValue;
        }
    }
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.help_text').forEach(function (el) {
        el.style.cursor = 'pointer';

        el.addEventListener('click', function () {
            window.open('/privacy', '_blank');
        });
    });
});
</script> 