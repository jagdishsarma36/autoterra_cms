@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<section class="legal-hero">
  <div class="legal-hero-glow"></div>
  <div class="legal-hero-inner">
    <div class="sec-eye" style="margin-bottom:12px;">{{ pageContent('global', 'privacy.hero_eyebrow') }}</div>
    <h1>{!! pageContent('global', 'privacy.hero_heading') !!}</h1>
    <p class="legal-hero-meta">{{ pageContent('global', 'privacy.hero_description') }}</p>
  </div>
</section>


@include('partials.footer')
@endsection
