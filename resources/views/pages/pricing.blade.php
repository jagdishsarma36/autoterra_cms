@extends('layouts.app')
@section('title', 'Pricing — AutoTerra')
@section('body')
@include('partials.nav')

<!-- Pricing hero Section -->
@foreach(pageContentJson('pricing', 'hero.sec_heading_text') as $hero)
<section class="pr-hero">
  <div class="sec-eye">{{ $hero['eyebrow'] }}</div>
  <h1>{!! $hero['heading'] !!}</h1>
  <p>{!! $hero['description'] !!}</p>
  <div class="pr-region-note">
    <i class="ti ti-world"></i>
    <span>{!! $hero['region_note'] !!}</span>
  </div>
</section>
@endforeach

<!-- Pricing toggle Section -->
@foreach(pageContentJson('pricing', 'pricing.toggle') as $toggle)
<div class="pr-toggle-wrap">
  <span class="pr-toggle-label">{{ $toggle['label'] }}</span>
  <div class="pr-track-tabs">
    @foreach($toggle['tracks'] as $track)
        <button
            class="pr-track-tab {{ !empty($track['active']) ? 'active' : '' }}"
            id="{{ $track['button_id'] }}"
            onclick="setTrack('{{ $track['id'] }}')"
        >
            <i class="ti {{ $track['icon'] }}" ></i>
            {{ $track['text'] }}
        </button>
    @endforeach
  </div>
  <div class="pr-license-toggle">
     <label>
        <input
            type="checkbox"
            id="{{ $toggle['license_toggle']['id'] }}"
            onchange="toggleLicenseNote()"
        >
        {{ $toggle['license_toggle']['label'] }}
    </label>
  </div>
</div>
@endforeach

<!-- Pricing cards Section -->
@php
$pricing = pageContentJson('pricing', 'pricing.cards');
@endphp
<div class="pr-cards-wrap">
    {{-- Floating Note --}}
    @if(!empty($pricing['floating_note']))
    <div id="floatingNote" class="pr-floating-note">
        <i class="ti ti-info-circle"></i>
        <strong>{{ $pricing['floating_note']['title'] }}</strong>
        {{ $pricing['floating_note']['text'] }}
    </div>
    @endif
    @foreach($pricing['tracks'] as $track)
    <div class="pr-cards-line {{ $track['class'] }}" id="{{ $track['id'] }}">
        @if(!empty($track['description']))
        <p class="pr-track-description">
            {{ $track['description'] }}
        </p>
        @endif
        <div class="pr-cards-grid {{ $track['id'] == 'lidarTrack' ? 'track-lidar' : '' }}">
            @foreach($track['cards'] as $card)
            <div class="pr-card {{ !empty($card['popular']) ? 'popular' : '' }}">
                @if(!empty($card['popular']))
                <div class="pr-popular-flag">
                    {{ $card['popular_text'] }}
                </div>
                @endif
                <div class="pr-card-head">
                    <div class="pr-card-icon {{ $card['icon_color'] }}">
                        <i class="ti {{ $card['icon'] }}"></i>
                    </div>
                    <div class="pr-card-name">
                        {{ $card['name'] }}
                    </div>
                    <div class="pr-card-tagline">
                        {{ $card['tagline'] }}
                    </div>
                </div>
                <div class="pr-price-row {{ !empty($card['popular']) ? 'popular-price' : '' }}">
                    <div class="pr-price-text">
                        {{ $card['price'] }}
                    </div>
                    <div class="pr-price-note">
                        {{ $card['price_note'] }}
                    </div>
                </div>
                <div class="pr-features">
                    {{-- Included --}}
                    @foreach($card['included'] ?? [] as $feature)
                    <div class="pr-feat-item">
                        <i class="ti ti-check yes"></i>
                        {{ $feature }}
                    </div>
                    @endforeach
                    {{-- Excluded --}}
                    @if(!empty($card['excluded']))
                    <div class="pr-feat-sep">Not included</div>
                    @foreach($card['excluded'] as $feature)
                    <div class="pr-feat-item">
                        <i class="ti ti-minus no"></i>
                        {{ $feature }}
                    </div>
                    @endforeach
                    @endif
                </div>
                {{-- Coming Soon --}}
                @if(!empty($card['coming_soon']))
                <div class="pr-coming-soon">
                    <span class="ti {{ $card['coming_soon']['icon'] }}"></span>
                    <span style="font-size:11px;color:#9A5F10;font-weight:600;line-height:1.5;">
                        <strong>{{ $card['coming_soon']['title'] }}</strong>
                        — {{ $card['coming_soon']['text'] }}

                        <a href="{{ $card['coming_soon']['link_url'] }}">
                            {{ $card['coming_soon']['link_text'] }}
                        </a>
                    </span>
                </div>
                @endif
                <div class="pr-card-cta">
                    <a href="{{ $card['button']['url'] }}"
                       class="pr-cta-btn {{ $card['button']['class'] }}">
                        @if(!empty($card['button']['icon']))
                            <i class="ti {{ $card['button']['icon'] }}"></i>
                        @endif

                        {{ $card['button']['text'] }}
                    </a>
                    @if(!empty($card['details_link']))
                    <a href="{{ $card['details_link']['url'] }}" >
                        {{ $card['details_link']['text'] }}
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if(!empty($track['footer_note']))
        <p class="pr-track-footer-note">
            {{ $track['footer_note'] }}
            @if(!empty($track['footer_link']))
            <a href="{{ $track['footer_link']['url'] }}"
               style="color:var(--cyan);">
                {{ $track['footer_link']['text'] }}
            </a>
            @endif
        </p>
        @endif
    </div>
    @endforeach
</div>


<section style="background:var(--off);padding:56px 60px;">
  <div class="sec-eye">Questions</div>
  <h2 class="sec-h2" style="margin-bottom:24px;">Common pricing questions</h2>
  <div class="pric_sec">
    @foreach(pageContentJson('pricing', 'faq') as $faq)
    <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:22px;">
      <h4 style="font-size:14px;font-weight:700;color:var(--body);margin-bottom:8px;display:flex;align-items:center;gap:8px;">
        <i class="ti ti-help-circle" style="color:var(--cyan);font-size:16px;"></i>
        {{ $faq['question'] }}
      </h4>
      <p style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $faq['answer'] }}</p>
    </div>
    @endforeach
  </div>
</section>
<div style="background:var(--navy);padding:56px 60px;display:flex;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;">
  <div>
    <h3 style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.3px;margin-bottom:8px;">{{ pageContent('pricing', 'enterprise.heading') }}</h3>
    <p style="font-size:14px;color:rgba(210,230,248,0.45);max-width:500px;line-height:1.7;">{{ pageContent('pricing', 'enterprise.description') }}</p>
  </div>
  <div style="display:flex;gap:12px;flex-wrap:wrap;">
    <a href="/contact" class="btn-cyan">{{ pageContent('pricing', 'enterprise.button_primary_text') }}</a>
    <a href="/products" class="btn-ghost">{{ pageContent('pricing', 'enterprise.button_secondary_text') }}</a>
  </div>
</div>
@include('partials.footer')
@endsection
