@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<!-- solutions page hero section -->
<section class="sol-hero">
  <div class="sol-hero-inner">
    <h1>{!! pageContent('solutions', 'hero.heading') !!}</h1>
    <p>{{ pageContent('solutions', 'hero.description') }}</p>
  </div>
</section>
<!-- solutions page hero section end -->

@include('partials.footer')
@endsection
