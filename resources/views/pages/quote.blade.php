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
    $quoteData = pageContentJson('quote', 'quote.from_sec1');
    $quote = $quoteData[0] ?? [];
    @endphp
    <div class="product-grid" id="productGrid">
          @foreach($quote['products'] ?? [] as $product)
              <label
                  class="prod-card {{ !empty($product['selected']) ? 'selected' : '' }}"
                  data-product="{{ $product['id'] }}"
                  onclick="selectProduct(this)">
                  <input
                      type="radio"
                      name="product"
                      value="{{ $product['id'] }}"
                      {{ !empty($product['selected']) ? 'checked' : '' }}
                  >
                  <div class="prod-card-check"></div>
                  <div class="prod-card-name">
                      {{ $product['name'] }}
                  </div>
                  <div class="prod-card-track">
                      {{ $product['track'] }}
                  </div>
                  @if(!empty($product['badge']))
                      <div class="prod-card-badge">
                          {{ $product['badge'] }}
                      </div>
                  @endif
              </label>
          @endforeach
        </div>

        <!-- Feature preview (updates by JS) -->
        <div class="feature-compare" id="featureCompare">
          <div class="fc-header">
            <span class="ti ti-table"></span>
            <span class="fc-header-title" id="fcTitle">{{ $quote['feature_compare']['title'] }}</span>
          </div>
          <div class="fc-row">
            <div class="fc-feature">Feature</div>
            <div class="fc-col-head">Included</div>
          </div>
          <div id="fcRows">
            @foreach($quote['feature_compare']['features'] as $feature)
            <div class="fc-row">
                <div class="fc-feature">
                    {{ $feature['feature'] }}
                </div>
                @if($feature['included'])
                    <div class="fc-val yes">
                        <span class="ti ti-circle-check"></span> Yes
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
          {{ $quote['subscription_term']['description'] }}
        </p>
        <div class="term-pills" id="termPills">
          @foreach($quote['subscription_term']['terms'] as $term)
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
      </div>

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
            <p style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $field->help_text }}</p>
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
    <hr class="qt-sec-divider">
    <!-- STEP 5: Requirements & notes -->
    <!-- <div class="qt-sec-head">
      <div class="qt-sec-num">5</div>
      <div class="qt-sec-label">Additional requirements</div>
    </div> -->
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
