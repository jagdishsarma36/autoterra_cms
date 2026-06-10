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

<section style="background:var(--off);padding:56px 60px;">
  <div class="sec-eye">Questions</div>
  <h2 class="sec-h2" style="margin-bottom:24px;">Common pricing questions</h2>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
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
