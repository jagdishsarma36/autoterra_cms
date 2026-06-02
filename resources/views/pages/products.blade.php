@extends('layouts.app')
@section('title', 'Products — AutoTerra')
@section('body')
@include('partials.nav')
<div class="page-hero"><div class="sec-eye">{{ pageContent('products', 'hero.eyebrow') }}</div><h1>{{ pageContent('products', 'hero.heading') }}</h1><p>{{ pageContent('products', 'hero.description') }}</p></div>
<section class="section section-light"><div class="sec-eye">{{ pageContent('products', 'tracks.eyebrow') }}</div><h2 class="sec-h2">{{ pageContent('products', 'tracks.heading') }}</h2><p class="sec-sub">{{ pageContent('products', 'tracks.description') }}</p></section>
<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('products', 'cta.heading') }}</h2><p>{{ pageContent('products', 'cta.description') }}</p><div class="cta-row"><a href="/buy" class="btn-cyan">{{ pageContent('products', 'cta.button_primary_text') }}</a><a href="/contact" class="btn-ghost">{{ pageContent('products', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
