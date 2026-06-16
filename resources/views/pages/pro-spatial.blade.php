@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<!-- hero section -->
@foreach(pageContentJson('pro_spatial', 'spatial.hero') as $hero)
<section class="ps-hero">
    <div class="ps-hero-bg">
        <div class="ph img_pro">
        @if(!empty($hero['image_url']))
            @if(Str::contains($hero['image_url'], '<iframe'))
                {!! $hero['image_url'] !!}
            @elseif(preg_match('/\.(mp4|webm|ogg)$/i', $hero['image_url']))
                <video autoplay muted loop playsinline controls>
                    <source src="{{ asset($hero['image_url']) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <img src="{{ asset($hero['image_url']) }}" alt="{{ $hero['title'] }}">
            @endif
            @elseif(!empty($hero['image']))
                <img src="{{ asset($hero['image']) }}" alt="{{ $hero['title'] }}">
            @endif
        </div>
        </div>
    </div>
    <div class="ps-hero-overlay"></div>
    <div class="ps-hero-content">
        <div class="ps-badge-row">
            <span class="ps-badge">{{ $hero['badge'] }}</span>
            <span class="ps-badge-tier">{{ $hero['badge_tier'] }}</span>
        </div>
        <h1>
            {!! $hero['title'] !!}
        </h1>
        <p>{{ $hero['description'] }}</p>
        <div class="ps-hero-btns">
            @foreach($hero['buttons'] as $button)
                <a href="{{ $button['url'] }}" class="{{ $button['class'] }}">
                    @if(isset($button['icon']))
                        <i class="ti {{ $button['icon'] }}"></i>
                    @endif
                    {{ $button['text'] }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endforeach


@include('partials.footer')
@endsection
