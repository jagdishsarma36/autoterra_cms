@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')
<section class="res-hero">
  <div class="sec-eye">{{ pageContent('resources', 'resources.hero_eyebrow') }}</div>
  <h1>{!! pageContent('resources', 'resources.hero_heading') !!}</h1>
  <p>{{ pageContent('resources', 'resources.hero_description') }}</p>
</section>
@include('partials.footer')
@endsection
