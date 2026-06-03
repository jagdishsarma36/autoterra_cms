@extends('layouts.app')
@section('title', 'AutoTerra — Survey. Model. Deliver.')
@section('body')
@include('partials.nav')

<section class="hero-wrap hero-bg ph">
  @php
    $videoUrl = pageContent('home', 'hero.video_url');
  @endphp
  @if(!empty($videoUrl))
  <div class="bg-video">
      <video autoplay muted loop playsinline preload="auto">
          <source src="{{ $videoUrl }}" type="video/mp4">
      </video>
  </div>
  @endif
  <div class="hero-wrap-inner">
  <div class="hero-bg-box" style="height:540px;border-radius:0;border:none;">
    <div class="hero-content">
      <div class="hero-pill">
        <i class="ti ti-planet" style="font-size:13px;"></i>
        {{ pageContent('home', 'hero.pill_text') }}
      </div>
      <h1 class="hero-h1">{!! pageContent('home', 'hero.heading') !!}</h1>
      <p class="hero-sub">
        {{ pageContent('home', 'hero.subheading') }}
      </p>
      <div class="hero-btns">
        <a href="/products" class="btn-cyan">{{ pageContent('home', 'hero.button_primary_text') }}</a>
        <button href="#" class="btn-ghost">{{ pageContent('home', 'hero.button_secondary_text') }}</button>
      </div>
    </div>
  </div>
</div>
</section>

<section class="section section-stats">
  <div class="stats">
    @foreach(pageContentJson('home', 'stats') as $stat)
    <div class="stat-item">
      <div class="stat-num">{{ $stat['number'] }}</div>
      <div class="stat-lbl">{{ $stat['label'] }}</div>
    </div>
    @endforeach
  </div>
</section>

<section class="section section-light">
    <div class="feat-grid">
      <div>
        <div class="sec-eye">{{ pageContent('home', 'feature1.eyebrow') }}</div>
        <h2 class="sec-h2">{!! pageContent('home', 'feature1.heading') !!}</h2>
        <p style="font-size:14px;color:#5A7A96;line-height:1.7;margin-bottom:28px;">
          {{ pageContent('home', 'feature1.description') }}
        </p>
        <div class="feat-list">
          @foreach(pageContentJson('home', 'feature1.features') as $feature)
          <div class="feat-item">
            <div class="feat-icon blue"><i class="ti {{ $feature['icon'] }}" style="font-size:20px;"></i></div>
            <div>
              <h4>{{ $feature['title'] }}</h4>
              <p>{{ $feature['description'] }}</p>
            </div>
          </div>
          @endforeach
        </div>
        <a href="{{ pageContent('home', 'feature1.link_url') }}" style="display:inline-flex;align-items:center;gap:6px;color:#00A8F8;font-size:14px;font-weight:700;margin-top:24px;">
          {{ pageContent('home', 'feature1.link_text') }} <i class="ti ti-arrow-right" style="font-size:14px;"></i>
        </a>
      </div>

      <!-- DEVELOPER: Replace with AutoTerra Pro Spatial screenshot -->
      @php
          $media = pageContent('home', 'feature1.image_url');
      @endphp

      <div class="ph" style="height:360px;">
          @if(!empty($media))
              {{-- YouTube/Vimeo iframe embed code --}}
              @if(Str::contains($media, '<iframe'))
                  {!! $media !!}

              {{-- Video file --}}
              @elseif(preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $media))
                  <video
                      autoplay
                      muted
                      loop
                      playsinline
                      controls
                      style="width:100%;height:100%;object-fit:cover;"
                  >
                      <source src="{{ $media }}">
                  </video>

              {{-- Image --}}
              @else
                  <img
                      src="{{ $media }}"
                      alt="AutoTerra Pro Spatial — point cloud classification view"
                      style="width:100%;height:100%;object-fit:cover;"
                  >
              @endif
          @endif
      </div>
    </div>
  </section>

<section class="section section-white">
    <div class="feat-grid">
      @php
          $media_1 = pageContent('home', 'feature2.image_url');
      @endphp
      <!-- DEVELOPER: Replace with terrain/contour screenshot -->
      <div class="ph feat-img-right" style="height:360px;">
        @if(!empty($media_1))
              {{-- YouTube/Vimeo iframe embed code --}}
              @if(Str::contains($media_1, '<iframe'))
                  {!! $media_1 !!}

              {{-- Video file --}}
              @elseif(preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $media_1))
                  <video
                      autoplay
                      muted
                      loop
                      playsinline
                      controls
                      style="width:100%;height:100%;object-fit:cover;"
                  >
                      <source src="{{ $media_1 }}">
                  </video>

              {{-- Image --}}
              @else
                  <img
                      src="{{ $media_1 }}"
                      alt="AutoTerra Pro Spatial — point cloud classification view"
                      style="width:100%;height:100%;object-fit:cover;"
                  >
              @endif
          @endif
      </div>

      <div>
        <div class="sec-eye">{{ pageContent('home', 'feature2.eyebrow') }}</div>
        <h2 class="sec-h2">{{ pageContent('home', 'feature2.heading') }}</h2>
        <p style="font-size:14px;color:#5A7A96;line-height:1.7;margin-bottom:28px;">
          {{ pageContent('home', 'feature2.description') }}
        </p>
        <div class="check-list">
          @foreach(pageContentJson('home', 'feature2.checklist') as $checkfeature)
            <div class="check-item"><i class="ti ti-check"></i> {{ $checkfeature }}</div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

<!-- slider section -->
  <section class="section section-dark">
      <div class="sec-eye" style="color:#00A8F8;">{{ pageContent('home', 'slider.howitworks.checkeye') }}</div>
      <h2 class="sec-h2" style="color:#ffffff;margin-bottom:8px;">{{ pageContent('home', 'slider.howitworks.heading') }}</h2>
      <p style="font-size:14px;color:rgba(210,230,248,0.5);margin-bottom:28px;">
        {{ pageContent('home', 'slider.howitworks.summary') }}
      </p>
      <div class="carousel-wrap">
        <div class="carousel-bar">
          <div class="window-dots">
            <div class="wd" style="background:#FF5F57;"></div>
            <div class="wd" style="background:#FEBC2E;"></div>
            <div class="wd" style="background:#28C840;"></div>
          </div>
          <span class="carousel-slide-label" id="slide-label"></span>
          <div class="carousel-nav">
            <button class="carousel-btn" onclick="goSlide(-1)" aria-label="Previous">
              <i class="ti ti-chevron-left"></i>
            </button>
            <button class="carousel-btn" onclick="goSlide(1)" aria-label="Next">
              <i class="ti ti-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- DEVELOPER: Replace each .slide-ph with <img src="assets/screen-N.jpg" alt="..."> -->
        <div id="carousel-slides">
          @foreach(pageContentJson('home', 'slider.howitworks') as $featureslider) 
            <div class="slide ph" style="height:380px;border-radius:0;border:none;border-bottom:2px dashed rgba(0,168,248,0.18);" data-title="{{ $featureslider['title'] }}" data-desc="{{ $featureslider['description'] }}">
              <img src="{{ $featureslider['url'] }}" alt="Feature {{ $loop->iteration }}" style="width:100%;height:100%;object-fit:cover;border-radius:0;">
            </div>
          @endforeach
        </div>

        <div class="carousel-dots" id="carousel-dots">
          <div class="cdot active"></div>
          <div class="cdot"></div>
          <div class="cdot"></div>
          <div class="cdot"></div>
        </div>
      </div>
    </section>

  <!-- PRODUCT FAMILY SECTION -->
<section class="section section-light">
    <div class="sec-eye">{{pageContent('home', 'products.eyebrow')}}</div>
      <h2 class="sec-h2">{{pageContent('home', 'products.heading')}}</h2>
      <p class="sec-sub">
        {{pageContent('home', 'products.description')}}
      </p>
    <div class="prod-grid">
      @foreach(pageContentJson('home', 'products.items') as $item)
        <div class="prod-card wrap-{{ filled($item['badge_class'] ?? null) ? $item['badge_class'] : 'default-prod' }} {{ $loop->last ? 'prod-card-last' : '' }}"> 
          <div class="prod-track">{{ $item['label'] }}</div>
          <span class="prod-badge {{ filled($item['badge_class'] ?? null) ? $item['badge_class'] : 'pb-std' }}">{{ $item['badge'] }}</span>
          <div class="prod-name">{{ $item['title'] }}</div>
          <div class="prod-tag">{{ $item['summary'] }}</div>
          <a href="{{ $item['link_url'] }}" class="prod-link">{{ $item['link_text'] }} <i class="ti ti-arrow-right" style="font-size:12px;"></i></a>
        </div>
      @endforeach
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="section section-white">
    <div class="sec-eye">{{ pageContent('home', 'testimonial.subhead') }}</div>
    <h2 class="sec-h2">{{ pageContent('home', 'testimonial.heading') }}</h2>

    <!-- Client logos -->
    <div class="logos-strip" style="margin-bottom:48px;padding:24px;background:#F5F8FC;border-radius:12px;">
      @foreach(pageContentJson('home', 'testimonial.clients_logo') as $logo)
      <div class="ph logo-ph">
        <img src="{{ $logo }}" alt="Client logo" style="height:100%;object-fit:contain;">
      </div>
      @endforeach
    </div>

    <div class="testi-slider-wrapper">
        <div class="testi-grid" id="testi-grid">
            @foreach(pageContentJson('home', 'testimonials') as $testimonial)
            <div class="testi-card">
                <p class="testi-quote">"{{ $testimonial['quote'] }}"</p>
                <div class="testi-author">
                    <div class="testi-avatar ph" style="width:44px;height:44px;border-radius:50%;min-height:auto;padding:0;border-width:1px;flex-shrink:0;">
                        <img src="{{ $testimonial['author_img'] }}" alt="Avatar of {{ $testimonial['author_name'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    </div>
                    <div>
                        <div class="testi-name">{{ $testimonial['author_name'] }}</div>
                        <div class="testi-org">{{ $testimonial['author_org'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="testi-controls">
      <button class="testi-btn" onclick="goTesti(-1)" aria-label="Previous testimonial">
        <i class="ti ti-chevron-left"></i>
      </button>
      <!-- <div class="testi-dots">
        <div class="tdot"></div>
        <div class="tdot active"></div>
        <div class="tdot"></div>
      </div> -->
      @php
      $testimonials = pageContentJson('home', 'testimonials');
      $pages = ceil(count($testimonials) / 2);
      @endphp
      <div class="testi-dots">
          @for($i = 0; $i < $pages; $i++)
              <div class="tdot {{ $i == 0 ? 'active' : '' }}"></div>
          @endfor
      </div>
      <button class="testi-btn" onclick="goTesti(1)" aria-label="Next testimonial">
        <i class="ti ti-chevron-right"></i>
      </button>
    </div>
  </section>
  <!--testimonials section end-->
  

<section class="cta-band">
  <div class="cta-band-inner">
    <h2>{{ pageContent('home', 'cta.heading') }}</h2>
    <p>{{ pageContent('home', 'cta.description') }}</p>
    <div class="cta-row">
      <a href="/buy" class="btn-cyan">{{ pageContent('home', 'cta.button_primary_text') }}</a>
      <a href="/contact" class="btn-ghost">{{ pageContent('home', 'cta.button_secondary_text') }}</a>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection

@section('scripts')
<script>
document.getElementById('menuToggle').addEventListener('click', function() {
  const links = document.querySelector('.nav-links');
  if (!links.style.display || links.style.display === 'none') {
    links.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:56px;left:0;right:0;background:var(--navy);padding:16px 24px;gap:14px;border-bottom:1px solid rgba(0,168,248,0.12);z-index:99;';
  } else { links.style.display = 'none'; }
});
</script>
@endsection
