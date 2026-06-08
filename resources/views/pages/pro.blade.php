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

<section class="section section-light">
  <div class="sec-eye">{{ pageContent('pro', 'section1.eyebrow') }}</div>
  <h2 class="sec-h2">{{ pageContent('pro', 'section1.heading') }}</h2>
  <p class="sec-sub">{{ pageContent('pro', 'section1.description') }}</p>
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
