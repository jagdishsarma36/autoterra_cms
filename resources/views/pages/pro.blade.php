@extends('layouts.app')
@section('title', 'AutoTerra Pro — AutoTerra')
@section('body')
@include('partials.nav')
<!-- Hero Section -->
<section class="pro-hero">
  <div class="pro-hero-bg">
    <div class="ph" style="height:100%;min-height:480px;border-radius:0;border:none;">
      <img src="{{ pageContent('pro', 'hero.sec_img') }}" alt="Hero Background" ">
    </div>
  </div>
  <div class="pro-hero-overlay"></div>
  <div class="pro-hero-content">
    <div class="pro-badge-row">
      <span class="pro-badge">{{ pageContent('pro', 'hero.badge') }}</span>
    </div>
    <h1>{!! pageContent('pro', 'hero.heading') !!}</h1>
    <p>{{ pageContent('pro', 'hero.description') }}</p>
    <div class="pro-hero-btns">
    @foreach(pageContentJson('pro', 'hero.button_primary_text') as $button)
    <a href="{{ $button['bttn_url'] }}" class="{{ $button['class'] }}">
        @if(!empty($button['icon_class']))
            <i class="ti {{ $button['icon_class'] }}"></i>
        @endif
        {{ $button['bttn_text'] }}
    </a>
    @endforeach
    </div>
  </div>
</section>

<!-- Differentiators -->
<div class="pro-diff">
  <div class="pro-diff-inner">
    <span class="pro-diff-label">{{ pageContent('pro', 'differentiators.heading') }}</span>
    <div class="pro-diff-items">
      @foreach(pageContentJson('pro', 'differentiators.items') as $item)
      <span class="pro-diff-item"><i class="ti {{ $item['icon_class'] }}"></i> {{ $item['text'] }}</span>
      @endforeach
    </div>
  </div>
</div>

<!-- Sections content all-->
@php
    $contents = pageContentJson('pro', 'section1.content');
    $sections = [];
    $current = null;
    foreach ($contents as $item) {
        if (isset($item['sec_heading'])) {
            if ($current) {
                $sections[] = $current;
            }
            $current = $item;
            $current['features'] = [];
        } else {
            $current['features'][] = $item;
        }
    }
    if ($current) {
        $sections[] = $current;
    }
@endphp
@foreach($sections as $section)
<section class="pro-section {{ $loop->odd ? 'section-white' : 'section-light' }}">
    <div class="pro-eyebrow">{{ $section['sec_eyebrow'] ?? '' }}</div>
    <div class="pro-grid">
        <div>
            <h3 class="pro-h3">{{ $section['sec_heading'] ?? '' }}</h3>
            <p class="pro-desc">
                {{ $section['sec_text'] ?? '' }}
            </p>
            <div class="pro-feat-list">
                @foreach($section['features'] as $feature)
                    <div class="pro-feat-item">
                        <i class="ti ti-circle-check-filled"></i>
                        <div>
                            <div class="lbl">{{ $feature['heading'] ?? '' }}</div>
                            <div class="sub">{{ $feature['text'] ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="ph" style="height:460px;">
                @if(!empty($section['image_url']))
                    <img src="{{ $section['image_url'] }}" alt="section image">
                @endif
            </div>
        </div>
    </div>
</section>
@endforeach
<!-- Sections content all end-->

<!-- capabilities -->
 <section class="pro-caps">
  <div style="text-align:center;margin-bottom:0;">
    @foreach(pageContentJson('pro', 'capabilities.contents') as $cap)
      <div class="sec-eye" style="text-align:center;">{{ $cap['cap_eyebrow'] }}</div>
      <h2 class="sec-h2 sec-h2-light" style="text-align:center;">{{ $cap['cap_heading'] }}</h2>
      <p class="sec-sub sec-sub-light" style="text-align:center;max-width:520px;margin:0 auto;">
        {{ $cap['cap_text'] }}
      </p>
      <div class="pro-caps-grid">
        @foreach($cap['features'] as $feature)
          <div class="pro-cap-card">
            <div class="pro-cap-icon">
              <i class="ti {{ $feature['icon_class'] }}"></i>
            </div>
            <h4>{{ $feature['heading'] }}</h4>
            <p>{{ $feature['text'] }}</p>
            <div class="pro-cap-features">
              @foreach($feature['items'] as $item)
                <span class="pro-cap-feat">
                  <i class="ti ti-check"></i> {{ $item }}
                </span>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    @endforeach
  </div>
</section>

<section class="section section-white">
  <div class="sec-eye">{{ pageContent('pro', 'who.eyebrow') }}</div>
  <h2 class="sec-h2">{{ pageContent('pro', 'who.heading') }}</h2>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:28px;">
    @foreach(pageContentJson('pro', 'who.personas') as $persona)
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
      <h3 style="font-size:16px;font-weight:800;margin-bottom:8px;">{{ $persona['title'] }}</h3>
      <p style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $persona['description'] }}</p>
    </div>
    @endforeach
  </div>
</section>

<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('pro', 'cta.heading') }}</h2><p>{{ pageContent('pro', 'cta.description') }}</p><div class="cta-row"><a href="/buy" class="btn-cyan">{{ pageContent('pro', 'cta.button_primary_text') }}</a><a href="/contact" class="btn-ghost">{{ pageContent('pro', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
