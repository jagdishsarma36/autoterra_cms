@extends('layouts.app')
@section('title', 'AutoTerra')

@section('body')
@include('partials.nav')

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

@php
  $buttons = pageContentJson('resources', 'resources.buttons');
@endphp

<section class="res-tabs" id="resTabs">
  @if(!empty($buttons))
    @foreach($buttons as $index => $btn)
      <button 
        class="res-tab {{ $index === 0 ? 'active' : '' }}"
        onclick="scrollToRes('{{ trim($btn['link_url']) }}', this)">
        <i class="ti {{ $btn['icon'] }}"></i>
        {{ trim($btn['tsxt']) }}
      </button>
    @endforeach
  @endif
</section>

@include('partials.footer')
@endsection