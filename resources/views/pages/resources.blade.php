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

    @if(!empty($docs) && is_array($docs))

      @foreach($docs as $doc)

        <div class="res-doc-card">

          <div class="res-doc-icon cyan">
            <i class="ti {{ $doc['icon'] ?? '' }}"></i>
          </div>

          <div class="res-doc-content">
            <h4>{{ $doc['title'] ?? '' }}</h4>
            <p>{{ $doc['description'] ?? '' }}</p>

            <a href="{{ $doc['link_url'] ?? '#' }}" class="res-link">
              {{ $doc['link_text'] ?? 'Open guide' }}
              <i class="ti ti-arrow-right"></i>
            </a>
          </div>

        </div>

      @endforeach

    @endif

  </div>

</section>

@include('partials.footer')
@endsection