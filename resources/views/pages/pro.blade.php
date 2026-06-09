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

@foreach(pageContentJson('pro', 'section1.content') as $sec_content)
<!-- section1 content -->
<section class="pro-section {{ $loop->odd ? 'section-white' : 'section-light' }} ">
  <div class="pro-eyebrow">{{ $sec_content['sec_eyebrow'] ?? '' }}</div>
  <div class="pro-grid">
    <div>
      <h3 class="pro-h3">{{ $sec_content['sec_heading'] ?? '' }}</h3>
      <p class="pro-desc">{{ $sec_content['sec_text'] ?? '' }}</p>
      <div class="pro-feat-list">
      
        <div class="pro-feat-item">
          <i class="ti ti-circle-check-filled"></i>
          <div>
            <div class="lbl">{{ $sec_content['heading'] ?? '' }}</div>
            <div class="sub">{{ $sec_content['text'] ?? '' }}</div>
          </div>
        </div>
               
      </div>
    </div>
    <div>
      <div class="ph" style="height:460px;">
        @if(!empty($sec_content['image_url']))
        <img src="{{ $sec_content['image_url'] }}" alt="sec1_img">
        @endif
      </div>
    </div>
  </div>
</section>
@endforeach 

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
