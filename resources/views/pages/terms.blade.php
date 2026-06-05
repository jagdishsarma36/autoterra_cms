@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<section class="legal-hero">
  <div class="legal-hero-glow"></div>
  <div class="legal-hero-inner">
    <div class="sec-eye" style="margin-bottom:12px;">
      {{ pageContent('global', 'terms.header_eyebrow') }}
    </div>
    <h1>{!! pageContent('global', 'terms.hero_heading') !!}</h1>
    <p class="legal-hero-meta">
      {{ pageContent('global', 'terms.herosub_description') }}
    </p>
  </div>
</section>

<section>
<div class="legal-wrap">

  <!-- Sidebar TOC -->
  <nav class="legal-toc">
    <div class="legal-toc-title">On this page</div>
    @foreach(pageContentJson('global', 'terms.description') as $item)
      <a href="#terms_{{$loop->index}}">
        {{ $item['title'] }}
      </a>
    @endforeach
  </nav>

  <!-- Main content -->
  <div class="legal-content">

    <!-- Short Description -->
    <div class="legal-notice">
      @php
        $short = pageContent('global', 'terms.short_description');
      @endphp

      @if(Str::contains($short, '<'))
        {!! $short !!}
      @else
        {!! '<p>' . implode('</p><p>', explode("\n", e($short))) . '</p>' !!}
      @endif
    </div>

    <!-- Prepare Links -->
    @php
      $links = pageContentJson('global', 'terms.descriptions.links') ?? [];
    @endphp

    <!-- Description Sections -->
    @foreach(pageContentJson('global', 'terms.description') as $item)

      <h2 id="terms_{{$loop->index}}">
        {{$loop->iteration}}.{{$item['title']}}
      </h2>

      @php
        // Detect HTML
        $hasHtml = Str::contains($item['description'], '<');

        // Use raw HTML OR escaped text
        $content = $hasHtml ? $item['description'] : e($item['description']);

        // Replace matching text with links
        foreach ($links as $link) {
            if (!empty($link['link_text']) && !empty($link['link_url'])) {

                $anchor = '<a href="'.$link['link_url'].'" class="legal-link"'
                        .(Str::startsWith($link['link_url'], 'http') ? ' target="_blank"' : '')
                        .'>'.$link['link_text'].'</a>';

                $content = str_replace($link['link_text'], $anchor, $content);
            }
        }

        // Convert line breaks to paragraphs (only for plain text)
        if (!$hasHtml) {
            $content = '<p>' . implode('</p><p>', explode("\n", $content)) . '</p>';
        }
      @endphp

      {!! $content !!}

      @if(!$loop->last)
        <hr class="legal-hr">
      @endif

    @endforeach

  </div><!-- /legal-content -->

</div>
</section>

@include('partials.footer')
@endsection