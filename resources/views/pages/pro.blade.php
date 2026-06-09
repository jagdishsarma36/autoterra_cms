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
    <p class="sec-sub sec-sub-light" style="text-align:center;max-width:520px;margin:0 auto;">{{ $cap['cap_text'] }}</p>
    <div class="pro-caps-grid">
      @foreach($cap['features'] as $feature)
      <div class="pro-cap-card">
        <div class="pro-cap-icon"><i class="ti {{ $feature['icon_class'] }}"></i></div>
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

<!-- who is it for section -->
@foreach(pageContentJson('pro', 'who.content') as $who)
<section class="pro-who">
  <div class="sec-eye">{{ $who['who_eyebrow'] }}</div>
  <h2 class="sec-h2">{{ $who['who_heading'] }}</h2>
  <p class="sec-sub">{{ $who['who_text'] }}</p>
  <div class="pro-who-grid">
     @foreach($who['who_cards'] as $card)
    <div class="pro-who-card">
      <i class="ti {{ $card['icon'] }}"></i>
      <h4>{{ $card['heading'] }}</h4>
      <p>{{ $card['text'] }}</p>
    </div>
    @endforeach
  </div>
</section>
@endforeach

<!--pro section -->
@foreach(pageContentJson('pro', 'pro.content') as $pro_content)
<section class="pro-position section-white">
  <div class="sec-eye">{{ $pro_content['pro_eyebrow'] }}</div>
  <h2 class="sec-h2">{{ $pro_content['pro_heading'] }}</h2>
  <p class="sec-sub">{{ $pro_content['pro_text'] }}</p>
  <div class="pro-ladder">
    @foreach($pro_content['ladder_steps'] as $step)
    <div class="{{ $step['class'] ?? 'pro-ladder-step above' }}">
      <div class="pro-step-label">{{ $step['label'] }}</div>
      <div class="pro-step-name">{{ $step['name'] }}</div>
      <div class="pro-step-desc">{{ $step['description'] }}</div>
    </div>
    @endforeach
  </div>
  <div class="pro-upgrade-note">
    <i class="ti ti-info-circle"></i>
    <span>{!! $pro_content['upgrade_note'] !!}</span>
  </div>
</section>
@endforeach

<!-- specs pro -->
@foreach(pageContentJson('pro', 'prospecs.content') as $spec)
<section class="pro-specs">
  <div class="sec-eye">{{ $spec['specs_eyebrow'] ?? '' }}</div>
  <h2 class="sec-h2">{{ $spec['specs_heading'] ?? '' }}</h2>
  <p class="sec-sub">{!! $spec['specs_text'] ?? '' !!}</p>
  <table class="pro-specs-table">
    <thead>
      <tr>
          <th style="width:220px;">Capability</th>
          <th>Detail</th>
      </tr>
    </thead>
    <tbody>
      @foreach($spec['specifications'] ?? [] as $item)
        <tr>
            <td class="spec-cat">{{ $item['capability'] ?? '' }}</td>
            <td class="spec-val">{{ $item['detail'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</section>
@endforeach

<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('pro', 'cta.heading') }}</h2><p>{{ pageContent('pro', 'cta.description') }}</p><div class="cta-row"><a href="/buy" class="btn-cyan">{{ pageContent('pro', 'cta.button_primary_text') }}</a><a href="/contact" class="btn-ghost">{{ pageContent('pro', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
