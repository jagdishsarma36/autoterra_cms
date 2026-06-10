@extends('layouts.app')
@section('title', 'AutoTerra')

@section('body')
@include('partials.nav')

{{-- HERO --}}
<section class="res-hero">
  <div class="sec-eye">
    {{ pageContent('resources', 'resources.hero_eyebrow') }}
  </div>

  <h1>
    {!! pageContent('resources', 'resources.hero_heading') !!}
  </h1>

  <p>
    {{ pageContent('resources', 'resources.hero_description') }}
  </p>
</section>

{{-- TABS --}}
@php
  $buttons = pageContentJson('resources', 'resources.buttons');

  if (is_string($buttons)) {
      $buttons = json_decode($buttons, true);
  }
@endphp

<section class="res-tabs" id="resTabs">
  @if(!empty($buttons) && is_array($buttons))
    @foreach($buttons as $index => $btn)
      <a 
        href="#{{ trim($btn['link_url'] ?? '') }}"
        class="res-tab {{ $index === 0 ? 'active' : '' }}"
      >
        <i class="ti {{ $btn['icon'] ?? '' }}"></i>
        {{ trim($btn['link_text'] ?? $btn['tsxt'] ?? '') }}
      </a>
    @endforeach
  @endif
</section>

{{-- DOCS SECTION --}}
@php
  $docs = pageContentJson('resources', 'reources.documentation');
  if (!is_array($docs)) {
      $docs = [];
  }
@endphp
@foreach($docs as $res_doc)
<section class="res-section section-white" id="docs">
  <div class="sec-eye">{{ $res_doc['sec_eye'] ?? '' }}</div>
  <h2 class="sec-h2">{{ $res_doc['head'] ?? '' }}</h2>
  <p class="sec-sub">{{ $res_doc['description'] ?? '' }}</p>
  <div class="res-doc-grid">
    @php
      $cards = $res_doc['list'] ?? [];
      if (!is_array($cards)) {
          $cards = [];
      }
    @endphp
    @foreach($cards as $card)
      <a href="{{ $card['link_url'] ?? '#' }}" class="res-doc-card cus_{{ str_replace(' ', '_', $card['icon'] ?? 'default') }}">
        <div class="res-doc-icon cyan"><i class="ti {{ $card['icon'] ?? '' }}"></i></div>
        <h4>{{ $card['title'] ?? '' }}</h4>
        <p>{{ $card['list_description'] ?? '' }}</p>
        <span class="res-link">
          {{ $card['link_text'] ?? 'Open guide' }}
          <i class="ti ti-arrow-right" style="font-size:12px;"></i>
        </span>
      </a>
    @endforeach
  </div>
</section>
@endforeach
@php
  $videosSection = pageContentJson('resources', 'resources.videos');

  if (!is_array($videosSection)) {
      $videosSection = [];
  }

  $videos = $videosSection['videos'] ?? [];
@endphp

<section class="res-section section-light" id="videos">
  <div class="sec-eye">
    {{ $videosSection['sec_eye'] ?? '' }}
  </div>
  <h2 class="sec-h2">
    {{ $videosSection['head'] ?? '' }}
  </h2>
  <p class="sec-sub">
    {{ $videosSection['description'] ?? '' }}
  </p>
  <div class="res-video-grid">
    @foreach($videos as $video)
      <a href="{{ $video['media_url'] ?? '#' }}" class="res-video-card">
        <div class="res-video-thumb">
          {{-- Thumbnail --}}
            @if(!empty($video['image_url']))
            @if(Str::contains($video['image_url'], '<iframe'))
            {!! $video['image_url'] !!}
            @elseif(preg_match('/\.(mp4|webm|ogg)$/i', $video['image_url']))
           <video playsinline controls>
            <source src="{{ $video['image_url'] }}">
            Your browser does not support the video tag.
          </video>
            @else
              <img src="{{ $video['image_url'] }}" alt="section image">
            @endif
            @endif
        </div>
        <div class="res-video-body">
          <div class="tag">
            {{ $video['tag'] ?? '' }}
          </div>
          <h4>
            {{ $video['title'] ?? '' }}
          </h4>
          <div class="meta">
            <i class="{{ $video['icon'] ?? 'ti ti-clock' }}"></i>
            {{ $video['duration'] ?? '' }} · {{ $video['quality'] ?? '' }} · {{ $video['year'] ?? '' }}
          </div>
        </div>
      </a>
    @endforeach
  </div>
  <div style="text-align:center;margin-top:28px;">
    <a href="{{ $videosSection['youtube']['link_url'] ?? '#' }}" class="btn-outline">
      <i class="ti ti-brand-youtube" style="font-size:15px;vertical-align:-2px;"></i>
      {{ $videosSection['youtube']['text'] ?? 'View all tutorials' }}
    </a>
  </div>
</section>

@include('partials.footer')
@endsection