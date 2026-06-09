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
<section class="res-section section-white" id="docs">
@foreach(pageContentJson('resources', 'resources.documentation') as $res_doc)
  <div class="sec-eye">{{  $res_doc['sec_eye']  }}</div>
  <h2 class="sec-h2">{{ $res_doc['head'] }}</h2>
  <p class="sec-sub">{{  $res_doc['description']  }}</p>
  <div class="res-doc-grid">
    @foreach($res_doc['list'] as $card)
    <a href="#" class="res-doc-card">
      <div class="res-doc-icon cyan"><i class="ti {{ $card['icon'] }}"></i></div>
      <h4>{{ $card['title'] }}</h4>
      <p>{{ $card['description'] }}</p>
      <span class="res-link">{{ $card['link_text'] }} <i class="ti ti-arrow-right"></i></span>
    </a>
    @endforeach
  </div>
</section>
@endforeach

@include('partials.footer')
@endsection