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
  @foreach(pageContentJson('eula', 'eula.right_content') ?? [] as $eulaRightContent)

    <h2 id="{{ $eulaRightContent['id'] ?? '' }}">
        {{ $eulaRightContent['title'] ?? '' }}
    </h2>

    @foreach($eulaRightContent['content'] ?? [] as $paragraph)
        <p>{{ $paragraph }}</p>
    @endforeach

    @if(!empty($eulaRightContent['list']))
        <ul>
            @foreach($eulaRightContent['list'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($eulaRightContent['note']))
        <p>{{ $eulaRightContent['note'] }}</p>
    @endif

    @foreach($eulaRightContent['additional'] ?? [] as $additional)
        <p>{{ $additional }}</p>
    @endforeach

    @if(!empty($eulaRightContent['contact']))
        <ul>
            @if(!empty($eulaRightContent['contact']['email']))
                <li>
                    <strong>Email:</strong>
                    <a href="mailto:{{ $eulaRightContent['contact']['email'] }}">
                        {{ $eulaRightContent['contact']['email'] }}
                    </a>
                </li>
            @endif

            @if(!empty($eulaRightContent['contact']['address']))
                <li>
                    <strong>Post:</strong>
                    {{ $eulaRightContent['contact']['address'] }}
                </li>
            @endif
        </ul>
    @endif

    @unless($loop->last)
        <hr class="legal-hr">
    @endunless

@endforeach
</div>

@include('partials.footer')
@endsection
