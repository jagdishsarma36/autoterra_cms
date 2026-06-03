@extends('layouts.app')
@section('title', 'About — AutoTerra')
@section('body')
@include('partials.nav')
<section class="page-hero" style="background:var(--navy);padding:60px;">
  <div class="sec-eye">{{ pageContent('about', 'hero.eyebrow') }}</div>
  <h1 style="font-size:38px;font-weight:800;color:#fff;margin-bottom:14px;">{{ pageContent('about', 'hero.heading') }}</h1>
  <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ pageContent('about', 'hero.description') }}</p>
</section>

<!-- Story Section -->
<section class="abt-story section-white">
  <div>
    <div class="sec-eye">{{ pageContent('about', 'story.eyebrow') }}</div>
    <h2>{!! pageContent('about', 'story.heading') !!}</h2>
     @foreach(pageContentJson('about', 'story.paragraphs') as $p)
      <p>{{ $p }}</p>
      @endforeach
    <div style="margin-top:28px;">
        @php
            $link = pageContentJson('about', 'story.link');
        @endphp
        <a href="{{ $link['url'] }}" class="btn-cyan">
            {{ $link['text'] }}
        </a>
    </div>
  </div>
  <div>
    <div class="abt-timeline">
      @foreach(pageContentJson('about', 'timeline') as $timelineItem)
      <div class="abt-tl-item {{ $loop->last ? 'timeline-last' : '' }}">
        <div class="abt-tl-dot"></div>
        <div class="abt-tl-year">{{ $timelineItem['year'] }}</div>
        <div class="abt-tl-text">{{ $timelineItem['text'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>
<!-- story section end -->

<!-- stats section -->
<section class="section section-light" style="padding:60px;">
<div class="abt-stats">
   @foreach(pageContentJson('about', 'stats') as $stat)
  <div>
    <div class="abt-stat-num">{{ $stat['number'] }}</div>
    <div class="abt-stat-lbl">{{ $stat['label'] }}</div>
  </div>
  @endforeach
</div>
</section>
<!-- stats section end -->

<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('about', 'cta.heading') }}</h2><p>{{ pageContent('about', 'cta.description') }}</p><div class="cta-row"><a href="/contact" class="btn-cyan">{{ pageContent('about', 'cta.button_primary_text') }}</a><a href="/products" class="btn-ghost">{{ pageContent('about', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
