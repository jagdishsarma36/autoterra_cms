@extends('layouts.app')
@section('title', 'Request a Quote — AutoTerra')
@section('body')
@include('partials.nav')

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

    <div class="form-row">
      <div class="form-group">
        <label>First name <span class="req">*</span></label>
        <input type="text" id="firstName" placeholder="First name" required="">
      </div>
      <div class="form-group">
        <label>Last name <span class="req">*</span></label>
        <input type="text" id="lastName" placeholder="Last name" required="">
      </div>
    </div>

    <hr class="qt-sec-divider">

    <!-- STEP 5: Requirements & notes -->
    <div class="qt-sec-head">
      <div class="qt-sec-num">5</div>
      <div class="qt-sec-label">Additional requirements</div>
    </div>

    <div class="form-row full" style="margin-bottom:16px;">
      <div class="form-group">
        
      </div>
    </div>

    <!-- Privacy + submit -->

    <div class="qt-submit-row">
      <button type="button" class="qt-submit-btn" onclick="handleSubmit()">
        <span class="ti ti-send"></span>
        Submit Quote Request
      </button>
      <p class="qt-submit-note">Our sales team responds within <strong>1 business day</strong>.<br>No spam, no pressure.</p>
    </div>
    <!-- Success state -->
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

<section style="padding:56px 60px;max-width:800px;">
  <h2 style="font-size:18px;font-weight:800;margin-bottom:20px;">Quote request form</h2>
  <p style="font-size:13px;color:var(--muted);margin-bottom:24px;">Form placeholder — integrate with POST /api/quote endpoint.</p>
</section>
@include('partials.footer')
@endsection
