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

    @foreach(pageContentJson('global', 'terms.description') as $item)

      <h2 id="terms_{{$loop->index}}">
        {{$loop->iteration}}. {{$item['title']}}
      </h2>

      @php
        $text = $item['description'];

        if(Str::contains($text, '<')) {
            // HTML → render directly
            echo $text;
        } else {
            // Plain text → convert \n to paragraphs
            $paragraphs = explode("\n", $text);
            foreach ($paragraphs as $p) {
                if(trim($p) !== '') {
                    echo '<p>' . e($p) . '</p>';
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