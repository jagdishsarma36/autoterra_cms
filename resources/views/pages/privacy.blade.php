@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<section class="legal-hero">
  <div class="legal-hero-glow"></div>
  <div class="legal-hero-inner">
    <div class="sec-eye" style="margin-bottom:12px;">{{ pageContent('global', 'privacy.hero_eyebrow') }}</div>
    <h1>{!! pageContent('global', 'privacy.hero_heading') !!}</h1>
    <p class="legal-hero-meta">{{ pageContent('global', 'privacy.hero_description') }}</p>
  </div>
</section>

<section>
<div class="legal-wrap">

  <!-- Sidebar TOC -->
  <nav class="legal-toc">
    <div class="legal-toc-title">On this page</div>
    @foreach(pageContentJson('global', 'privacy.description') as $item)
      <a href="#terms_{{$loop->index}}">{{ $item['title'] }}</a>
    @endforeach
  </nav>

  <!-- Main content -->
  <div class="legal-content">

    <div class="legal-notice">
        @php
            $short = pageContent('global', 'privacy.short_description');
        @endphp

        @if(Str::contains($short, '<'))
            {!! $short !!}
        @else
            {!! '<p>' . implode('</p><p>', explode("\n", e($short))) . '</p>' !!}
        @endif
    </div>
     @if(pageContentJson('global', 'privacy.links'))
      <div class="legal-links">
        <ul>
          @foreach(pageContentJson('global', 'privacy.links') as $link)
            <li>
              <a href="{{ $link['link_url'] }}" 
                 class="legal-link"
                 @if(Str::startsWith($link['link_url'], 'http')) target="_blank" @endif>
                 
                {{ $link['link_text'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    @endif
    @foreach(pageContentJson('global', 'privacy.description') as $item)
      <h2 id="terms_{{$loop->index}}">{{$loop->iteration}}.{{$item['title']}}</h2>
        @if(Str::contains($item['description'], '<'))
        {!! $item['description'] !!}
        @else
            {!! '<p>' . implode('</p><p>', explode("\n", e($item['description']))) . '</p>' !!}
        @endif
      <hr class="legal-hr">
    @endforeach
  </div><!-- /legal-content -->
</div>
</section>


@include('partials.footer')
@endsection
