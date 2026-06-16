@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<!-- hero section -->
@foreach(pageContentJson('Pro Spatial', 'spatial.hero') as $hero)
<section class="ps-hero">
    <div class="ps-hero-bg">
        <div class="ph img_pro">
            <img src="{{ asset($hero['image']) }}" alt="{{ $hero['title'] }}">
        </div>
    </div>
    <div class="ps-hero-overlay"></div>
    <div class="ps-hero-content">
        <div class="ps-badge-row">
            <span class="ps-badge">{{ $hero['badge'] }}</span>
            <span class="ps-badge-tier">{{ $hero['badge_tier'] }}</span>
        </div>
        <h1>
            {{ $hero['title'] }}
            <span>{{ $hero['highlight_title'] }}</span>
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

<section style="background:var(--navy);padding:60px;"><h1 style="font-size:34px;font-weight:800;color:#fff;">{{ ucfirst(str_replace('-', ' ', request()->path())) }}</h1><p style="font-size:15px;color:rgba(210,230,248,0.5);margin-top:12px;">This page is under development.</p></section>

@include('partials.footer')
@endsection
