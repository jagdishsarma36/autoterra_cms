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

<section>
<div class="legal-wrap">

  <!-- Sidebar TOC -->
  <nav class="legal-toc">
    <div class="legal-toc-title">On this page</div>
    @foreach(pageContentJson('global', 'privacy.description') as $item)
      <a href="#terms_{{$loop->index}}">{{ $item['title'] }}</a>
    @endforeach
  </nav>

  <!-- Main content -->
  <div class="legal-content">

    <div class="legal-notice">
      <p>{{ pageContent('global', 'privacy.short_description') }}</p>
    </div>

    <h2 id="terms_{{$loop->index}}">{{$loop->iteration}}.{{$item['title']}}</h2>
    @foreach(pageContentJson('global', 'privacy.description') as $item)
      <p>{!! nl2br(e($item['description'])) !!}</p>
      <hr class="legal-hr">
    @endforeach
  </div><!-- /legal-content -->
</div>
</section>


@include('partials.footer')
@endsection
