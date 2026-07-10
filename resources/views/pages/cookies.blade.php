@extends('layouts.app')
@section('title', 'AutoTerra')
@section('meta_description', 'AutoTerra uses cookies to enhance your browsing experience and provide personalized content. Learn more about our cookie policy.')

@section('body')
@include('partials.nav')

<section class="legal-hero">
  <div class="legal-hero-glow"></div>
  <div class="legal-hero-inner">
    <div class="sec-eye" style="margin-bottom:12px;">
      {{ pageContent('cookies', 'cookies.hero_eyebrow') }}
    </div>
    <h1>{!! pageContent('cookies', 'cookies.hero_heading') !!}</h1>
    <p class="legal-hero-meta">
      {{ pageContent('cookies', 'cookies.hero_description') }}
    </p>
  </div>
</section>

<section>
<div class="legal-wrap">

  <!-- Sidebar TOC -->
  <nav class="legal-toc">
    <div class="legal-toc-title">On this page</div>
    @foreach(pageContentJson('cookies', 'cookies.description') as $item)
      <a href="#terms_{{$loop->index}}">
        {{ $item['title'] }}
      </a>
    @endforeach
  </nav>

  <!-- Main content -->
  <div class="legal-content">

    @php
      $links = pageContentJson('cookies', 'cookies.links') ?? [];

      //  Build safe replacement map (LONGEST FIRST)
      $replaceMap = [];

      foreach ($links as $link) {
          if (!empty($link['link_text']) && !empty($link['link_url'])) {

              $replaceMap[$link['link_text']] =
                  '<a href="'.$link['link_url'].'" class="legal-link"'
                  .(Str::startsWith($link['link_url'], 'http') ? ' target="_blank"' : '')
                  .'>'.$link['link_text'].'</a>';
          }
      }

      // Sort by key length (avoid conflicts like "Privacy Policy" inside "Google Privacy Policy ↗")
      uksort($replaceMap, function ($a, $b) {
          return strlen($b) - strlen($a);
      });
    @endphp

    <!-- Short Description -->
    <div class="legal-notice">
      @php
        $short = pageContent('cookies', 'cookies.short_description');

        $hasHtml = Str::contains($short, '<');
        $shortContent = $hasHtml ? $short : e($short);

        //  Safe replacement
        $shortContent = strtr($shortContent, $replaceMap);

        if (!$hasHtml) {
            $shortContent = '<p>' . implode('</p><p>', explode("\n", $shortContent)) . '</p>';
        }
      @endphp

      {!! $shortContent !!}
    </div>

    <!-- Prepare Data -->
    @php
      $tables = pageContentJson('cookies', 'cookies.table') ?? [];
      $tableParagraph = pageContent('cookies', 'cookies.table.p');
    @endphp

    <!-- Description Sections -->
    @foreach(pageContentJson('cookies', 'cookies.description') as $item)

      <h2 id="terms_{{$loop->index}}">
        {{$loop->iteration}}. {{$item['title']}}
      </h2>

      @php
        $hasHtml = Str::contains($item['description'], '<');
        $content = $hasHtml ? $item['description'] : e($item['description']);

        //  Safe replacement
        $content = strtr($content, $replaceMap);

        if (!$hasHtml) {
            $content = '<p>' . implode('</p><p>', explode("\n", $content)) . '</p>';
        }
      @endphp

      {!! $content !!}

      {{-- TABLE INSERTION IN SECTION 4 --}}
      @if($loop->iteration == 4 && !empty($tables))
        <div class="legal-content">

          <table class="cookie-table">
            <thead>
              <tr>
                @foreach(array_keys($tables[0]) as $heading)
                  <th>{{ $heading }}</th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach($tables as $row)
                <tr>
                  @foreach($row as $key => $cell)

                    {{-- Badge for Type --}}
                    @if($key === 'Type')
                      @php
                        $value = strtolower($cell);
                        $class = str_contains($value, 'required') ? 'badge-req' : 'badge-opt';
                      @endphp

                      <td>
                        <span class="{{ $class }}">
                          {{ $cell }}
                        </span>
                      </td>
                    @else
                      <td>{{ $cell }}</td>
                    @endif

                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>

          {{-- PARAGRAPH BELOW TABLE --}}
          @if(!empty($tableParagraph))
            @php
              $hasHtml = Str::contains($tableParagraph, '<');
              $paraContent = $hasHtml ? $tableParagraph : e($tableParagraph);

              //  Safe replacement
              $paraContent = strtr($paraContent, $replaceMap);

              if (!$hasHtml) {
                  $paraContent = '<p>' . implode('</p><p>', explode("\n", $paraContent)) . '</p>';
              }
            @endphp

            <div class="cookie-table-note">
              {!! $paraContent !!}
            </div>
          @endif

        </div>
      @endif

      @if(!$loop->last)
        <hr class="legal-hr">
      @endif

    @endforeach

  </div><!-- /legal-content -->

</div>
</section>

@include('partials.footer')
@endsection