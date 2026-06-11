@extends('layouts.app')
@section('title', 'Request a Quote — AutoTerra')
@section('body')
@include('partials.nav')

<!-- Hero Section -->
@foreach(pageContentJson('quote', 'quote.hero_section') as $hero)
<section class="qt-hero">
  <div class="qt-hero-inner">
    <div class="qt-hero-badge">
      <span class="ti {{ $hero['icon'] }}"></span>
      {{ $hero['text'] }}
    </div>
    <h1>{{ $hero['title'] }}</h1>
    <p>{{ $hero['description'] }}</p>
    <div class="qt-trust">
      @foreach($hero['trust_items'] as $item)
        <div class="qt-trust-item">
          <span class="ti {{ $item['icon'] }}"></span>
          {{ $item['text'] }}
        </div>
      @endforeach
    </div>
  </div>
</section>
@endforeach

<section style="padding:56px 60px;max-width:800px;">
  <h2 style="font-size:18px;font-weight:800;margin-bottom:20px;">Quote request form</h2>
  <p style="font-size:13px;color:var(--muted);margin-bottom:24px;">Form placeholder — integrate with POST /api/quote endpoint.</p>
</section>
@include('partials.footer')
@endsection
