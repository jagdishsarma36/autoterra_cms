@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')

<!-- solutions page hero section -->
<section class="sol-hero">
  <div class="sol-hero-inner">
    <h1>{!! pageContent('solutions', 'hero.heading') !!}</h1>
    <p>{{ pageContent('solutions', 'hero.description') }}</p>
  </div>
</section>
<!-- solutions page hero section end -->

<!-- solutions layout -->
<div class="sol-layout">
  <!-- ── SIDEBAR ── -->
  <aside class="sol-sidebar">
    <div class="sol-sidebar-title">Industries</div>
    <nav class="sol-nav">
    @foreach(pageContentJson('solutions', 'solutions.side_menu') as $solutions)
      <a class="sol-nav-item active" href="{{ $solutions['url_link'] }}">
        <i class="ti {{ $solutions['icon_class'] }}"> </i> {{ $solutions['url_text'] }}
      </a>
    @endforeach
    </nav>
  </aside>
  <!-- ── CONTENT ── -->
  <div class="sol-content">
    <!-- ══ VERTICAL 1: SURVEY & MAPPING ══ -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_survey') as $survey)
    <section class="sol-section section-white" id="{{ $survey['id'] }}">
        <div class="sol-section-head">
            <div class="sol-vert-label cyan">
                <i class="ti {{ $survey['icon'] }}"></i>
                {{ $survey['title'] }}
            </div>
            <h2>{{ $survey['heading'] }}</h2>
            <p>{{ $survey['description'] }}</p>
        </div>
        <div class="sol-section-inner">
            {{-- Left Content --}}
            <div>
                <div class="sol-workflow">
                    @foreach($survey['workflow'] as $workflow)
                    <div class="sol-workflow-step">
                        <div class="sol-step-num cyan">
                            {{ $workflow['step'] }}
                        </div>
                        <div class="sol-step-body">
                            <strong>{{ $workflow['title'] }}</strong>
                            {{ $workflow['description'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="sol-deliverables">
                    @foreach($survey['deliverables'] as $deliverable)
                    <div class="sol-deliv-item">
                        <i class="ti ti-circle-check-filled" style="color:var(--cyan);"></i>
                        {{ $deliverable }}
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Right Content --}}
            <div>
                @if(!empty($survey['image_url']))
                    <div class="ph" style="height:360px;">
                        <img src="{{ ($survey['image_url']) }}"
                        alt="{{ $survey['title'] }}"
                        class="img-fluid">
                    </div>
                @endif
                <div class="sol-products-strip" style="margin-top:20px;">
                    <div class="sol-products-strip-lbl">
                        Recommended editions
                    </div>
                    <div class="sol-product-pills">
                        @foreach($survey['recommended_editions'] as $edition)
                        <a href="#" class="sol-pill">
                            <i class="ti ti-package"></i>
                            {{ $edition }}

                            @if($edition == 'Pro Spatial')
                                <span class="hot-dot"></span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        @if(!empty($survey['cta']))
        <div style="margin-top:24px;">
            <a href="{{ $survey['cta_url'] ?: '#' }}" class="btn-cyan">
                {{ $survey['cta'] }}
            </a>
        </div>
        @endif
    </section>
    @endforeach
<!-- ══ VERTICAL 1: SURVEY & MAPPING END══ -->

<!-- ══ VERTICAL 2: ROADS & HIGHWAY ══ -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_roads') as $road)
    <section class="sol-section section-light" id="{{ $road['id'] }}">
        <div class="sol-section-head">
            <div class="sol-vert-label amber">
                <i class="ti {{ $road['icon'] }}"></i>
                {{ $road['title'] }}
            </div>
            <h2>{{ $road['heading'] }}</h2>
            <p>{{ $road['description'] }}</p>
        </div>
        <div class="sol-section-inner reverse">
            {{-- Left Side --}}
            <div>
                @if(!empty($road['image_url']))
                    <div class="ph" style="height:360px;">
                        <img src="{{ ($road['image_url']) }}"
                        alt="{{ $road['title'] }}"
                        style="width:100%;border-radius:12px;">
                    </div>
                @endif
                @if(!empty($road['recommended_editions']))
                    <div class="sol-products-strip" style="margin-top:20px;">
                        <div class="sol-products-strip-lbl">
                            Recommended editions
                        </div>
                        <div class="sol-product-pills">
                            @foreach($road['recommended_editions'] as $edition)
                                <a href="#" class="sol-pill">
                                    <i class="ti ti-package"></i>
                                    {{ $edition }}

                                    @if($edition == 'Pro Spatial')
                                        <span class="hot-dot"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            {{-- Right Side --}}
            <div>
                @if(!empty($road['stats']))
                    <div class="sol-stats-row">
                        @foreach($road['stats'] as $stat)
                            <div class="sol-stat">
                                <div class="sol-stat-num" style="color:var(--amber);">
                                    {{ $stat['value'] }}
                                </div>
                                <div class="sol-stat-lbl">
                                    {{ $stat['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($road['workflow']))
                    <div class="sol-workflow">
                        @foreach($road['workflow'] as $step)
                            <div class="sol-workflow-step">
                                <div class="sol-step-num amber">
                                    {{ $step['step'] }}
                                </div>
                                <div class="sol-step-body">
                                    <strong>{{ $step['title'] }}</strong>
                                    {{ $step['description'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($road['cta']))
                    <a href="{{ url('contact') }}"
                      class="btn-cyan"
                      style="margin-top:20px;display:inline-block;">
                        {{ $road['cta'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>
    @endforeach

<!-- ══ VERTICAL 3: LIDAR AERIAL MAPPING ══ -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_lidar') as $lidar)
    <section class="sol-section section-white" id="{{ $lidar['id'] }}">
        <div class="sol-section-head">
            <div class="sol-vert-label {{ $lidar['theme'] }}">
                <i class="ti {{ $lidar['icon'] }}"></i> {{ $lidar['title'] }}
            </div>
            <h2>{{ $lidar['heading'] }}</h2>
            <p>{{ $lidar['description'] }}</p>
        </div>
        <div class="sol-section-inner">
            <!-- Left Content -->
            <div>
                {{-- Stats --}}
                @if(!empty($lidar['stats']))
                <div class="sol-stats-row">
                    @foreach($lidar['stats'] as $stat)
                    <div class="sol-stat">
                        <div class="sol-stat-num" style="color: var(--green);">
                          
                            {!! $stat['value'] !!}
                        </div>
                        <div class="sol-stat-lbl">
                            {{ $stat['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                {{-- Workflow --}}
                @if(!empty($lidar['workflow']))
                <div class="sol-workflow">
                    @foreach($lidar['workflow'] as $workflow)
                    <div class="sol-workflow-step">
                        <div class="sol-step-num {{ $lidar['theme'] }}">
                            {{ $workflow['step'] }}
                        </div>

                        <div class="sol-step-body">
                            <strong>{{ $workflow['title'] }}</strong>
                            {{ $workflow['description'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                {{-- Deliverables --}}
                @if(!empty($lidar['deliverables']))
                <div class="sol-deliverables" style="margin-bottom:20px;">
                    @foreach($lidar['deliverables'] as $deliverable)
                    <div class="sol-deliv-item">
                        <i class="ti ti-circle-check-filled"
                          style="color:var(--green);"></i>
                        {{ $deliverable }}
                    </div>
                    @endforeach
                </div>
                @endif
                {{-- CTA --}}
                @if(!empty($lidar['cta']))
                <a href="{{ $lidar['cta']['url'] }}" class="btn-cyan">
                    {{ $lidar['cta']['text'] }}
                </a>
                @endif
            </div>
            <!-- Right Content -->
            <div>
                @if(!empty($lidar['image_url']))
                    <div class="ph" style="height:420px;">
                        <img src="{{ ($lidar['image_url']) }}"
                        alt="{{ $lidar['title'] }}"
                        class="img-fluid">
                    </div>
                @endif
                {{-- Recommended Editions --}}
                @if(!empty($lidar['recommended_editions']))
                <div class="sol-products-strip" style="margin-top:20px;">
                    <div class="sol-products-strip-lbl">
                        Recommended editions
                    </div>
                    <div class="sol-product-pills">
                        @foreach($lidar['recommended_editions'] as $edition)
                        <a href="{{ $edition['url'] }}" class="sol-pill">
                            <i class="ti {{ $edition['icon'] }}"></i>
                            {{ $edition['name'] }}
                            @if(!empty($edition['featured']))
                                <span class="hot-dot"></span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endforeach

    <!-- ══ VERTICAL 4: MINING ══ -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_mining') as $mining)
    <section class="sol-section section-light" id="{{ $mining['id'] }}">
        <div class="sol-section-head">
            <div class="sol-vert-label {{ $mining['theme'] }}">
                <i class="ti {{ $mining['icon'] }}"></i>
                {{ $mining['title'] }}
            </div>
            <h2>{{ $mining['heading'] }}</h2>
            <p>{{ $mining['description'] }}</p>
        </div>
        <div class="sol-section-inner reverse">
            <!-- Left Side -->
            <div>
                @if(!empty($mining['image_url']) && $mining['image_url'] != '#')
                    <div class="ph" style="height:360px;">
                        <img src="{{ $mining['image_url'] }}"
                        alt="{{ $mining['title'] }}"
                        class="img-fluid">
                    </div>
                @endif
                @if(!empty($mining['recommended_editions']))
                    <div class="sol-products-strip" style="margin-top:20px;">
                        <div class="sol-products-strip-lbl">Recommended editions</div>
                        <div class="sol-product-pills">
                            @foreach($mining['recommended_editions'] as $edition)
                                <a href="{{ $edition['url'] }}" class="sol-pill">
                                    <i class="ti ti-package"></i>
                                    {{ $edition['name'] }}
                                    @if(!empty($edition['highlight']))
                                        <span class="hot-dot"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <!-- Right Side -->
            <div>
                @if(!empty($mining['workflow']))
                    <div class="sol-workflow">
                        @foreach($mining['workflow'] as $step)
                            <div class="sol-workflow-step">
                                <div class="sol-step-num {{ $mining['theme'] }}">
                                    {{ $step['step'] }}
                                </div>
                                <div class="sol-step-body">
                                    <strong>{{ $step['title'] }}</strong>
                                    {{ $step['description'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($mining['deliverables']))
                    <div class="sol-deliverables" style="margin:20px 0;">
                        @foreach($mining['deliverables'] as $deliverable)
                            <div class="sol-deliv-item">
                                <i class="ti ti-circle-check-filled"
                                  style="color:var(--blue);"></i>
                                {{ $deliverable }}
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($mining['cta']))
                    <a href="{{ $mining['cta']['url'] }}" class="btn-cyan">
                        {{ $mining['cta']['text'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>
    @endforeach

    <!-- ══ VERTICAL 5: GOVERNMENT & INFRASTRUCTURE ══ -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_govt') as $govt)
    <section class="sol-section section-white" id="{{ $govt['id'] }}">
        <div class="sol-section-head">
            <div class="sol-vert-label {{ $govt['theme'] }}">
                <i class="ti {{ $govt['icon'] }}"></i> {{ $govt['title'] }}
            </div>
            <h2>{{ $govt['heading'] }}</h2>
            <p>{{ $govt['description'] }}</p>
        </div>
        <div class="sol-section-inner">
            <div>
                {{-- Workflow --}}
                <div class="sol-workflow">
                    @foreach($govt['workflow'] as $workflow)
                        <div class="sol-workflow-step">
                            <div class="sol-step-num {{ $govt['theme'] }}">
                                {{ $workflow['step'] }}
                            </div>

                            <div class="sol-step-body">
                                <strong>{{ $workflow['title'] }}</strong>
                                {{ $workflow['description'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Deliverables --}}
                <div class="sol-deliverables" style="margin:20px 0;">
                    @foreach($govt['deliverables'] as $deliverable)
                        <div class="sol-deliv-item">
                            <i class="ti ti-circle-check-filled"
                              style="color:#7C4DBC;"></i>
                            {{ $deliverable }}
                        </div>
                    @endforeach
                </div>
                {{-- CTA --}}
                <a href="{{ $govt['cta']['link'] }}" class="btn-cyan">
                    {{ $govt['cta']['text'] }}
                </a>
            </div>
            <div>
                {{-- Image --}}
                @if(!empty($govt['image_url']))
                    <div class="ph" style="height:420px;">
                        <img src="{{ ($govt['image_url']) }}"
                        alt="{{ $govt['title'] }}"
                        class="img-fluid">
                    </div>
                @endif
                {{-- Products --}}
                <div class="sol-products-strip" style="margin-top:20px;">
                    <div class="sol-products-strip-lbl">
                        Recommended editions
                    </div>
                    <div class="sol-product-pills">
                        @foreach($govt['products'] as $product)
                            <a href="{{ $product['link'] }}" class="sol-pill">
                                <i class="ti {{ $product['icon'] }}"></i>
                                {{ $product['name'] }}

                                @if(!empty($product['featured']))
                                    <span class="hot-dot"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endforeach

<!-- uav section -->
    @foreach(pageContentJson('solutions', 'solutions.rightcontent_uav') as $uav)
    <section class="sol-section section-light" id="{{ $uav['id'] }}">
        <div class="sol-section-inner">
            <div class="sol-section-head">
                <div class="sol-eyebrow">{{ $uav['title'] }}</div>
                <h2>{{ $uav['heading'] }}</h2>
                @foreach($uav['description'] as $desc)
                    <p @if(!$loop->first) style="margin-top:12px;" @endif>
                        {{ $desc }}
                    </p>
                @endforeach
                {{-- Image --}}
                <div class="ph" style="height:360px;margin-top:24px;margin-bottom:24px;">
                    <img src="{{ ($uav['image_url']) }}"
                        alt="{{ $uav['title'] }}"
                        class="img-fluid">
                </div>
                {{-- Steps --}}
                <div class="sol-steps">
                    @foreach($uav['steps'] as $step)
                        <div class="sol-step">
                            <span class="sol-step-n">{{ $step['step'] }}</span>
                            <div>
                                <strong>{{ $step['title'] }}</strong> — {{ $step['description'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Notice --}}
                @if(!empty($uav['notice']))
                    <div style="background:#FEF9F0;border:1px solid rgba(200,122,32,0.28);border-radius:10px;padding:16px 20px;margin-top:20px;display:flex;gap:12px;align-items:flex-start;">
                        <span class="ti ti-info-circle" style="color:var(--amber);font-size:18px;margin-top:2px;flex-shrink:0;"></span>
                        <div>
                            <div style="font-size:13px;font-weight:800;color:#7A4800;margin-bottom:4px;">
                                🕐 {{ $uav['notice']['title'] }}
                            </div>
                            <div style="font-size:13px;color:#7A4800;line-height:1.6;">
                                {{ $uav['notice']['description'] }}
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Deliverables --}}
                @if(!empty($uav['deliverables']))
                    <div class="sol-deliverables" style="margin-top:24px;">
                        <div class="sol-del-title">Key deliverables</div>
                        <ul class="sol-del-list">
                            @foreach($uav['deliverables'] as $deliverable)
                                <li>{{ $deliverable }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- Products --}}
                @if(!empty($uav['products']))
                    <div class="sol-products-strip" style="margin-top:20px;">
                        <div class="sol-products-strip-lbl">Required editions (base + add-on)</div>
                        <div class="sol-product-pills">
                            @foreach($uav['products'] as $product)
                                <a href="{{ $product['url'] }}" class="sol-pill">
                                    <i class="ti ti-package"></i>
                                    {{ $product['name'] }}

                                    @if(!empty($product['featured']))
                                        <span class="hot-dot"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endforeach

  </div>
</div>

@include('partials.footer')
@endsection
