@extends('layouts.app')
@section('title', 'AutoTerra Pro — AutoTerra')
@section('body')
@include('partials.nav')
<section class="page-hero">
  <div class="sec-eye">{{ pageContent('pro', 'hero.badge') }}</div>
  <h1>{{ pageContent('pro', 'hero.heading') }}</h1>
  <p>{{ pageContent('pro', 'hero.description') }}</p>
  <div style="display:flex;gap:14px;margin-top:24px;">
    <a href="/quote" class="btn-cyan">{{ pageContent('pro', 'hero.button_primary_text') }}</a>
    <a href="/contact" class="btn-ghost" style="color:rgba(210,230,248,0.65);border-color:rgba(210,230,248,0.25);">{{ pageContent('pro', 'hero.button_secondary_text') }}</a>
  </div>
</section>
<section class="section section-light">
  <div class="sec-eye">{{ pageContent('pro', 'section1.eyebrow') }}</div>
  <h2 class="sec-h2">{{ pageContent('pro', 'section1.heading') }}</h2>
  <p class="sec-sub">{{ pageContent('pro', 'section1.description') }}</p>
</section>
<section class="section section-white">
  <div class="sec-eye">{{ pageContent('pro', 'who.eyebrow') }}</div>
  <h2 class="sec-h2">{{ pageContent('pro', 'who.heading') }}</h2>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:28px;">
    @foreach(pageContentJson('pro', 'who.personas') as $persona)
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
      <h3 style="font-size:16px;font-weight:800;margin-bottom:8px;">{{ $persona['title'] }}</h3>
      <p style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $persona['description'] }}</p>
    </div>
    @endforeach
  </div>
</section>
<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('pro', 'cta.heading') }}</h2><p>{{ pageContent('pro', 'cta.description') }}</p><div class="cta-row"><a href="/buy" class="btn-cyan">{{ pageContent('pro', 'cta.button_primary_text') }}</a><a href="/contact" class="btn-ghost">{{ pageContent('pro', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
