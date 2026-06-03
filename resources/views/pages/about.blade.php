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
<section class="section section-white abt-story" style="padding:60px;">
  <div>
    <div class="sec-eye">{{ pageContent('about', 'story.eyebrow') }}
        <h2 class="sec-h2">{!! pageContent('about', 'story.heading') !!}</h2>
        <div style="max-width:700px;margin-top:20px;">
          @foreach(pageContentJson('about', 'story.paragraphs') as $p)
          <p style="font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:16px;">{{ $p }}</p>
          @endforeach
        <div style="margin-top:28px;">
            @php
                $link = pageContentJson('about', 'story.link');
            @endphp

            <a href="{{ $link['url'] }}" class="btn-cyan">
                {{ $link['text'] }}
            </a>
        </div>
    <div> 
  </div>
  <div>
    <div class="abt-timeline">
        @foreach(pageContentJson('about', 'timeline') as $timelineItem)
        <div class="abt-tl-item">
            <div class="abt-tl-dot"></div>
            <div class="abt-tl-year">{{ $timelineItem['year'] }}</div>
            <div class="abt-tl-text">{{ $timelineItem['text'] }}</div>
        </div>
        @endforeach
    </div>
  </div>
  </div>
</section>
<!-- story section end -->

<section class="section section-light" style="padding:60px;">
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;max-width:900px;margin:0 auto;">
    @foreach(pageContentJson('about', 'stats') as $stat)
    <div style="text-align:center;"><div style="font-size:36px;font-weight:800;color:var(--cyan);">{{ $stat['number'] }}</div><div style="font-size:13px;color:var(--muted);">{{ $stat['label'] }}</div></div>
    @endforeach
  </div>
</section>
<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('about', 'cta.heading') }}</h2><p>{{ pageContent('about', 'cta.description') }}</p><div class="cta-row"><a href="/contact" class="btn-cyan">{{ pageContent('about', 'cta.button_primary_text') }}</a><a href="/products" class="btn-ghost">{{ pageContent('about', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
