@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<!-- eula hero section -->
<section class="legal-hero">
  <div class="legal-hero-glow"></div>
  <div class="legal-hero-inner">
    <div class="sec-eye" style="margin-bottom:12px;">{{ pageContent('eula', 'hero.pil_text') }}</div>
    <h1>{!! pageContent('eula', 'hero.heading') !!}</h1>
    <p class="legal-hero-meta">{!! pageContent('eula', 'hero.sub_heading') !!}</p>
  </div>
</section>
<!-- eula hero section end -->

@include('partials.footer')
@endsection
