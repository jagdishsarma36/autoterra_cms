@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

@php
  $form = App\Models\FormCms::where('slug', 'waitlist-form')->where('is_active', true)->first();
  $fields = $form ? $form->fields()->orderBy('sort_order')->get() : collect();
@endphp

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
                        <source src="{{ ($hero['image_url']) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ ($hero['image_url']) }}" alt="{{ $hero['title'] }}">
                @endif
                @elseif(!empty($hero['image']))
                    <img src="{{ ($hero['image']) }}" alt="{{ $hero['title'] }}">
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

<!-- starts -->
<div class="ps-stats">
    @foreach(pageContentJson('pro_spatial', 'spatial.stats') as $stat)
        <div class="ps-stat">
            <div class="ps-stat-num">
                {{ $stat['number'] }}
            </div>
            <div class="ps-stat-lbl">
                {{ $stat['label'] }}
            </div>
        </div>
    @endforeach
</div>

<!-- tabs -->
<div class="ps-tabs-wrap">
  <div class="ps-tabs" id="moduleTabs">
    @foreach(pageContentJson('pro_spatial', 'spatial.tabs') as $tab)
          <button
              class="ps-tab {{ !empty($tab['active']) ? 'active' : '' }}"
              onclick="scrollToModule('{{ $tab['target'] }}',this)"
              @if(!empty($tab['soon'])) style="position:relative;" @endif
          >
              {{ $tab['title'] }}
              @if(!empty($tab['soon']))
                  <span class=" spatial_tab ">
                      {{ $tab['soon_text'] ?? 'SOON' }}
                  </span>
              @endif
          </button>
      @endforeach
  </div>
</div>

<!-- FEAT SEC -->
@foreach(pageContentJson('pro_spatial', 'spatial.feat_sec') as $module)
<section class="feat-section {{ $module['section_class'] }}" id="{{ $module['id'] }}">
    <div class="feat-grid-lr {{ !empty($module['reverse']) ? 'reverse' : '' }}">
        @if(!empty($module['reverse']))
            <div>
                <div class="ph sec_spatial_img">
                    @if(!empty($module['image_url']))
                        @if(Str::contains($module['image_url'], '<iframe'))
                            {!! $module['image_url'] !!}
                        @elseif(preg_match('/\.(mp4|webm|ogg)$/i', $module['image_url']))
                            <video autoplay muted loop playsinline controls>
                                <source src="{{ $module['image_url'] }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $module['image_url'] }}" alt="autoterra">
                        @endif
                    @elseif(!empty($module['image']))
                        <img src="{{ $module['image'] }}" alt="autoterra">
                    @endif
                </div>
            </div>
        @endif

        <div>
            <div class="feat-eyebrow {{ $module['eyebrow_color'] }}">
                {{ $module['eyebrow'] }}
            </div>

            <h3 class="feat-h3">{{ $module['title'] }}</h3>

            <p class="feat-desc">
                {{ $module['description'] }}
            </p>

            @if(!empty($module['coming_soon']))
                <div class="spatial_soon">
                    <span class="ti ti-drone icn_spcl"></span>
                    <span class="spatial_cming">
                        {{ $module['coming_soon_text'] }}
                    </span>
                </div>
            @endif

            <div class="feat-chips">
                @foreach($module['chips'] as $chip)
                    <span class="feat-chip">{{ $chip }}</span>
                @endforeach
            </div>

            <div class="check-list">
                @foreach($module['features'] as $feature)
                    <div class="check-item">
                        <i class="ti ti-circle-check-filled"></i>
                        {{ $feature }}
                    </div>
                @endforeach
            </div>
        </div>
        @if(empty($module['reverse']))
            <div>
                <div class="ph sec_spatial_img">
                    @if(!empty($module['image_url']))
                        @if(Str::contains($module['image_url'], '<iframe'))
                            {!! $module['image_url'] !!}
                        @elseif(preg_match('/\.(mp4|webm|ogg)$/i', $module['image_url']))
                            <video autoplay muted loop playsinline controls>
                                <source src="{{ $module['image_url'] }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $module['image_url'] }}" alt="autoterra">
                        @endif
                    @elseif(!empty($module['image']))
                        <img src="{{ $module['image'] }}" alt="autoterra">
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endforeach

@include('partials.footer')
@endsection
