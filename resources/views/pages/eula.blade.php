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

<!-- eula content section -->
 <div class="legal-wrap">
  <nav class="legal-toc">
    @foreach(pageContentJson('eula', 'eula.nav') as $eulaNav)
        <div class="legal-toc-title">{{ $eulaNav['title'] }}</div>
        @foreach($eulaNav['links'] as $eulaNavItem)
            <a href="{{ $eulaNavItem['href'] }}">
                {{ $eulaNavItem['text'] }}
            </a>
        @endforeach
    @endforeach
  </nav>
  <div class="legal-content">
    <div class="legal-warn">
      <p>{!! pageContent('eula', 'eula.warning_text') !!}</p>
    </div>
  </div>
</div>

@include('partials.footer')
@endsection
