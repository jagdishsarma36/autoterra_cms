@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')

@include('partials.nav')

<section class="legal-hero">
  <div class="legal-hero-inner">
    <h1>{!! pageContent('global', 'terms.hero_heading') !!}</h1>
    <p>{{ pageContent('global', 'terms.hero_description') }}</p>
  </div>
</section>

<section>
<div class="legal-wrap">

  <!-- Sidebar -->
  <nav class="legal-toc">
    <div>On this page</div>
    @foreach(pageContentJson('global', 'terms.description') as $item)
      <a href="#terms_{{$loop->index}}">
        {{ $item['title'] }}
      </a>
    @endforeach
  </nav>

  <!-- Content -->
  <div class="legal-content">

    @php
      $links = pageContentJson('global', 'terms.links'); // load links
    @endphp

    @foreach(pageContentJson('global', 'terms.description') as $item)

      <h2 id="terms_{{$loop->index}}">
        {{$loop->iteration}}. {{$item['title']}}
      </h2>

      @php
        $text = $item['description'];

        // 🔥 APPLY LINKS
        if (!empty($links)) {
            foreach ($links as $link) {

                if (!empty($link['link_text']) && !empty($link['link_url'])) {

                    $url = $link['link_url'];

                    // Convert internal links to full URL
                    if (!\Illuminate\Support\Str::startsWith($url, ['http', 'mailto'])) {
                        $url = url($url);
                    }

                    $anchor = '<a href="'.$url.'" target="_blank">'.$link['link_text'].'</a>';

                    // Replace text with link
                    $text = str_replace($link['link_text'], $anchor, $text);
                }
            }
        }

        // ✅ RENDER CONTENT
        if(\Illuminate\Support\Str::contains($text, '<')) {
            echo $text;
        } else {
            $paragraphs = explode("\n", $text);
            foreach ($paragraphs as $p) {
                if(trim($p) !== '') {
                    echo '<p>' . $p . '</p>'; // no e()
                }
            }
        }
      @endphp

      @if(!$loop->last)
        <hr class="legal-hr">
      @endif

    @endforeach

  </div>

</div>
</section>

@include('partials.footer')

@endsection