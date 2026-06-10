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

{{-- DOWNLOADS SECTION --}}
@php
  $downloads = pageContentJson('resources', 'resources.downloads');
  if (!is_array($downloads)) {
      $downloads = [];
  }
@endphp

@foreach($downloads as $section)
<section class="res-section section-white" id="downloads">
  
  <div class="sec-eye">
    {{ $section['sec_eye'] ?? '' }}
  </div>

  <h2 class="sec-h2">
    {{ $section['head'] ?? '' }}
  </h2>

  <p class="sec-sub">
    {{ $section['description'] ?? '' }}
  </p>

  <div class="res-download-list">

    @if(!empty($section['list']) && is_array($section['list']))
      @foreach($section['list'] as $item)

        <div class="res-dl-row {{ !empty($item['locked']) ? 'is-locked' : '' }} cus_{{ str_replace(' ', '_', $item['icon'] ?? 'default') }}">

          {{-- ICON --}}
          <div class="res-dl-icon icon-{{ $item['icon'] ?? 'default' }}">
            <i class="ti {{ $item['icon'] ?? 'ti-point' }}"></i>
          </div>

          {{-- INFO --}}
          <div class="res-dl-info">
            <h4>
              {{ $item['title'] ?? '' }}

              @if(!empty($item['locked']))
                <i class="ti ti-lock icon-lock"></i>
              @endif
            </h4>

            <p>
              {{ $item['description'] ?? '' }}
            </p>
          </div>

          {{-- META --}}
          <div class="res-dl-meta">
            {{ $item['meta'] ?? '' }}
          </div>

          {{-- BUTTON / LOCKED --}}
          @if(!empty($item['locked']))
            <span class="res-locked">
              <i class="ti ti-lock"></i>
              {{ $item['locked_text'] ?? 'Locked' }}
            </span>
          @else
            <a href="{{ $item['link_url'] ?? '#' }}" class="res-dl-btn">
              <i class="ti ti-download"></i>
              {{ $item['link_text'] ?? 'Download' }}
            </a>
          @endif

        </div>

      @endforeach
    @endif

  </div>

  {{-- FOOTER --}}
 <p class="res-download-footer cus_{{ str_replace(' ', '_', $section['sec_eye'] ?? 'default') }}">
    <i class="ti ti-info-circle"></i>
    {{ $section['footer_note'] ?? '' }}
    <a href="{{ $section['footer_link_url'] ?? '#' }}">
      {{ $section['footer_link_text'] ?? '' }}
    </a>
  </p>
</section>
@endforeach
@php
  $releaseNotes = pageContentJson('resources', 'resources.releasenotes');

  if (!is_array($releaseNotes)) {
      $releaseNotes = [];
  }
@endphp

@foreach($releaseNotes as $section)
<section class="res-section section-light" id="{{ $section['id'] ?? 'releasenotes' }}">
  
  <div class="sec-eye">
    {{ $section['sec_eye'] ?? '' }}
  </div>

  <h2 class="sec-h2">
    {{ $section['head'] ?? '' }}
  </h2>

  <p class="sec-sub">
    {{ $section['description'] ?? '' }}
  </p>

  <div class="res-rn-list">
    
    @if(!empty($section['list']))
      @foreach($section['list'] as $item)
        <div class="res-rn-item {{ !empty($item['open']) ? 'open' : '' }}">
          
          <div class="res-rn-head" onclick="toggleRN(this)">
            <div class="res-rn-head-left">
              <span class="res-rn-version">
                {{ $item['version'] ?? '' }}
              </span>

              <span class="res-rn-date">
                {{ $item['date'] ?? '' }}
              </span>

              <span class="res-rn-title">
                {{ $item['title'] ?? '' }}
              </span>
            </div>

            <i class="ti ti-chevron-down res-rn-toggle"></i>
          </div>

          <div class="res-rn-body">
            <ul>
              @if(!empty($item['items']))
                @foreach($item['items'] as $li)
                  <li>{!! $li !!}</li>
                @endforeach
              @endif
            </ul>
          </div>

        </div>
      @endforeach
    @endif
  </div>
  <div style="margin-top:20px;">
    <a href="{{ $section['button']['link'] ?? '#' }}" class="btn-outline">
      {{ $section['button']['text'] ?? 'View full release history' }}
    </a>
  </div>

</section>
@endforeach
@php
  $section = pageContentJson('resources', 'resources.webinars');
@endphp

@if(!empty($section))
<section class="res-section section-white" id="{{ $section['id'] ?? 'webinars' }}">
  
  <div class="sec-eye">{{ $section['sec_eye'] ?? '' }}</div>

  <h2 class="sec-h2">{{ $section['head'] ?? '' }}</h2>

  <p class="sec-sub">{{ $section['description'] ?? '' }}</p>

  <div class="res-webinar-grid">

    @foreach($section['list'] ?? [] as $item)
      <a href="#" class="res-webinar-card">

        <div class="res-webinar-icon">
          <i class="ti {{ $item['icon'] ?? 'ti-video' }}"
             @if(($item['type'] ?? '') === 'upcoming')
               style="color:var(--green);"
             @endif
          ></i>
        </div>

        <div>
          <h4>{{ $item['title'] ?? '' }}</h4>
          <p>{{ $item['description'] ?? '' }}</p>

          <div class="res-webinar-date"
               @if(($item['cta_style'] ?? '') === 'highlight')
                 style="color:var(--green);"
               @endif>
            <i class="ti {{ $item['cta_icon'] ?? 'ti-player-play' }}"
               style="font-size:12px;vertical-align:-2px;"></i>
            {{ $item['cta_text'] ?? '' }}
          </div>
        </div>

      </a>
    @endforeach

  </div>
</section>
@endif
@endforeach
@include('partials.footer')
@endsection