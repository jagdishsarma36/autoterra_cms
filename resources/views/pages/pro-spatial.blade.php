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

<!-- carousel section -->
@php
    $carousel = pageContentJson('pro_spatial', 'spatial.carousel');
@endphp
<section class="ps-carousel-wrap">
    <div class="ps_caro_sp">
        <div class="sec-eye" style="text-align:center;">
            {{ $carousel['eye'] }}
        </div>
        <h2 class="sec-h2 sec-h2-light">
            {{ $carousel['title'] }}
        </h2>
        <p class="sec-sub sec-sub-light">
            {{ $carousel['subtitle'] }}
        </p>
    </div>
    <div class="ps-carousel-tabs" id="psCtabs">
        @foreach($carousel['slides'] as $index => $slide)
            <button
                class="ps-ctab {{ $index == 0 ? 'active' : '' }}"
                onclick="setPsSlide({{ $index }},this)"
            >
                {{ $slide['tab_title'] }}
            </button>
        @endforeach
    </div>
    <div class="ps-screen-frame">
        <div class="ps-screen-bar">
            <div class="ps-dot r"></div>
            <div class="ps-dot y"></div>
            <div class="ps-dot g"></div>
            <span class="ps-screen-title" id="psScreenTitle">
                {{ $carousel['slides'][0]['screen_title'] }}
            </span>
        </div>
        <div class="ps-screen-body">
            @foreach($carousel['slides'] as $index => $slide)
                <div
                    class="ps-slide {{ $index == 0 ? 'active' : '' }}"
                    id="psSlide{{ $index }}"
                    data-title="{{ $slide['screen_title'] }}"
                >
                    <div class="ph caro-spatial">
                        <img src="{{ $slide['image'] }}"
                             alt="{{ $slide['tab_title'] }}">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="ps-screen-nav">
        <button class="ps-nav-btn" onclick="movePsSlide(-1)">
            <i class="ti ti-chevron-left"></i>
        </button>
        <div class="ps-dots" id="psDots">
            @foreach($carousel['slides'] as $index => $slide)
                <div class="ps-dot-ind {{ $index == 0 ? 'active' : '' }}"></div>
            @endforeach
        </div>
        <button class="ps-nav-btn" onclick="movePsSlide(1)">
            <i class="ti ti-chevron-right"></i>
        </button>
    </div>
</section>

<!-- specks system -->
@foreach(pageContentJson('pro_spatial', 'spatial.technical_specs') as $spec)
<section class="specs-wrap" id="mod-specs">
    <div class="sec-eye">{{ $spec['eye'] }}</div>
    <h2 class="sec-h2">{{ $spec['title'] }}</h2>
    <p class="sec-sub">
        {{ $spec['subtitle'] }}
        <a class="specs_ural" href="{{ $spec['comparison_url'] }}">
            {{ $spec['comparison_text'] }}
        </a>
    </p>
    <table class="specs-table">
        <thead>
            <tr>
                <th class="sys_th">Category</th>
                <th>Specification</th>
            </tr>
        </thead>
        <tbody>
            @foreach($spec['specifications'] as $item)
            <tr>
                <td class="specs-cat">{{ $item['category'] }}</td>
                <td class="specs-val">{{ $item['value'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h3 class="sys_spactial">
        {{ $spec['system_requirements']['title'] }}
    </h3>
    <div class="sysreq-grid">
        @foreach(['minimum', 'recommended'] as $type)
        <div class="sysreq-card">
            <h4>
                <i class="ti {{ $type == 'minimum' ? 'ti-device-desktop' : 'ti-device-desktop-analytics' }}"></i>
                {{ $spec['system_requirements'][$type]['title'] }}
            </h4>
            @foreach($spec['system_requirements'][$type] as $key => $value)
                @if($key != 'title')
                <div class="sysreq-row">
                    <span class="lbl">{{ strtoupper($key) }}</span>
                    <span class="val">{{ $value }}</span>
                </div>
                @endif
            @endforeach
        </div>
        @endforeach
    </div>
</section>
@endforeach

<!-- products update section -->
@php
    $upgrade = pageContentJson('pro_spatial', 'spatial.upgrade');
@endphp
<section class="upgrade-wrap">
    <div class="sec-eye">{{ $upgrade['eye'] }}</div>
    <h2 class="sec-h2">{{ $upgrade['title'] }}</h2>
    <p class="sec-sub">{{ $upgrade['subtitle'] }}</p>
    <div class="upgrade-cards">
        @foreach($upgrade['cards'] as $card)
            <div class="upgrade-card {{ $card['hot'] ? 'hot' : '' }}">
                <div class="upgrade-card-head">
                    <h3>
                        {{ $card['title'] }}
                        @if($card['badge'])
                            <span class="badge badge-hot">{{ $card['badge'] }}</span>
                        @endif
                    </h3>
                    <p>{{ $card['description'] }}</p>
                </div>
                <div class="upgrade-card-body">
                    @foreach($card['features'] as $feature)
                        <div class="upgrade-item">
                            <i class="ti {{ $feature['available'] ? 'ti-check' : 'ti-x' }}"></i>
                            {{ $feature['text'] }}
                        </div>
                    @endforeach
                </div>
                <div class="upgrade-card-foot">
                    <a href="{{ $card['button_link'] }}" class="{{ $card['button_class'] }}">
                        {{ $card['button_text'] }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>

@include('partials.footer')
@endsection
