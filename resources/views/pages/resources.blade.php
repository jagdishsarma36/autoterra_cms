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
@endphp

<section class="res-tabs" id="resTabs">
  @if(!empty($buttons))
    @foreach($buttons as $index => $btn)
      <a 
        href="#{{ trim($btn['link_url']) }}"
        class="res-tab {{ $index === 0 ? 'active' : '' }}"
      >
        <i class="ti {{ $btn['icon'] }}"></i>
        {{ trim($btn['link_text'] ?? $btn['tsxt'] ?? '') }}
      </a>
    @endforeach
  @endif
</section>

{{-- DOCS SECTION --}}
<section class="res-section section-white" id="docs">
  <div class="sec-eye">
    {{ pageContent('resources', 'resources.page_seceye') }}
  </div>

  <h2 class="sec-h2">
    {{ pageContent('resources', 'resources.sec-h2') }}
  </h2>

  <p class="sec-sub">
    {{ pageContent('resources', 'resources.sec-sub') }}
  </p>

  @php
    $docs = pageContentJson('resources', 'resources.res_doc_grid');
  @endphp

  <div class="res-doc-grid">
    @if(!empty($docs))
      @foreach($docs as $doc)
        <div class="res-doc-card">

          <i class="ti {{ $doc['icon'] }}"></i>

          <div class="res-doc-content">
            <h3>{{ trim($doc['title']) }}</h3>
            <p>{{ trim($doc['description']) }}</p>

            <a href="{{ trim($doc['link_url']) }}" class="res-doc-link">
              {{ trim($doc['link_text']) }}
            </a>
          </div>

        </div>
      @endforeach
    @endif
  </div>
</section>

@include('partials.footer')
@endsection