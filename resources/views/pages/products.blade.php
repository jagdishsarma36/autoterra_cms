@extends('layouts.app')
@section('title', 'Products — AutoTerra')
@section('body')
@include('partials.nav')
<div class="page-hero"><div class="sec-eye">{{ pageContent('products', 'hero.eyebrow') }}</div><h1>{{ pageContent('products', 'hero.heading') }}</h1><p>{{ pageContent('products', 'hero.description') }}</p></div>

<!-- product content -->
<section class="section section-light">
    @foreach(pageContentJson('products', 'products.hero') as $trackSection)
    <div class="sec-eye">{{ $trackSection['eye'] }}</div>
    <h2 class="sec-h2">{{ $trackSection['title'] }}</h2>
    <p class="sec-sub">{{ $trackSection['subtitle'] }}</p>

    <div class="tracks">
        @foreach($trackSection['tracks'] as $track)
            <div class="track-card {{ $track['card_class'] ?? '' }}">
                <div class="track-header">
                    <div class="track-icon {{ $track['icon_class'] }}">
                        <i class="ti {{ $track['icon'] }}"></i>
                    </div>
                    <div>
                        <h3>{{ $track['title'] }}</h3>
                        <div class="sub">{{ $track['sub_title'] }}</div>
                    </div>
                </div>
                <p>{{ $track['description'] }}</p>
                <div class="track-products">
                    @foreach($track['products'] as $product)
                        <span class="track-pill {{ $product['class'] }}">
                            {{ $product['name'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endforeach

    <!-- COMPARISON TABLE -->
    <h2 class="sec-h2">Feature comparison</h2>
    <p class="pr_comparsion_text">All editions include the core CAD environment, DWG/DXF exchange and online map integration.</p>

    @php
    $comparison = pageContentJson('products', 'products.comparision');
    @endphp
    <div class="compare-wrap">
        <table class="compare-table">
            <thead>
                <tr>
                    <th class="feat-col" style="width:28%;">Feature</th>
                    @foreach($comparison['products'] as $index => $product)
                        <th class="{{ $index == 5 ? 'hot-col' : '' }}">
                            {{ $product }}

                            @if($product == 'Pro Spatial')
                                <span class="col-badge">Popular</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($comparison['categories'] as $category)
                    <tr class="cat-row">
                        <td colspan="{{ count($comparison['products']) + 1 }}">
                            {{ $category['title'] }}

                            @if(!empty($category['badge']))
                                <span class="pr_coming">
                                    {{ $category['badge'] }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @foreach($category['features'] as $feature)
                        <tr>
                            <td class="feat-col">
                                {{ $feature['name'] }}
                            </td>
                            @foreach($feature['values'] as $index => $value)
                                <td class="{{ $index == 5 ? 'hot-col' : '' }}">
                                    @if(strtolower($value) === 'yes')
                                        <i class="ti ti-check {{ $index == 5 ? 'tick-p' : 'tick' }}"></i>
                                    @elseif(strtolower($value) === 'no')
                                        <span class="dash">—</span>
                                    @elseif(strtolower($value) === 'soon')
                                        <span class="pr_soon">
                                            Soon
                                        </span>
                                    @else
                                        <span class="partial">
                                            {{ $value }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    <p style="font-size:12px;color:var(--muted);margin-top:10px;">
      <i class="ti ti-check tick" style="font-size:13px;"></i> Included &nbsp;&nbsp;
      <i class="ti ti-check tick-p" style="font-size:13px;"></i> Full/Advanced &nbsp;&nbsp;
      <span style="font-weight:600;color:var(--amber);">Partial</span> = limited capability &nbsp;&nbsp;
      — = not included &nbsp;&nbsp;
      <span style="font-size:11px;color:#9A5F10;font-weight:700;">Soon</span> = coming soon module
    </p>
  </section>

<!-- faq section -->
 <section class="section section-white">
    @foreach(pageContentJson('products', 'products.faq') as $faq)
    <div class="sec-eye">
        {{ $faq['faq_eye'] }}
    </div>
    <h2 class="sec-h2">
        {{ $faq['faq_title'] }}
    </h2>
    @endforeach
    <div class="faq-list">
       @foreach(pageContentJson('products', 'products.faq') as $faq)
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    {{ $faq['question'] }}
                    <i class="ti ti-plus"></i>
                </button>
                <div class="faq-a">
                    @if(!empty($faq['answer']))
                        {{ $faq['answer'] }}
                    @else
                        {{ $faq['answer_before_link'] ?? '' }}
                        @if(!empty($faq['link_text']) && !empty($faq['link_url']))
                            <a href="{{ $faq['link_url'] }}">
                                {{ $faq['link_text'] }}
                            </a>
                        @endif
                        {{ $faq['answer_after_link'] ?? '' }}
                    @endif
                </div>
            </div>
          @endforeach  
    </div>
</section>

<!-- cta section -->
<section class="cta-band"><div class="cta-band-inner"><h2>{{ pageContent('products', 'cta.heading') }}</h2><p>{{ pageContent('products', 'cta.description') }}</p><div class="cta-row"><a href="/buy" class="btn-cyan">{{ pageContent('products', 'cta.button_primary_text') }}</a><a href="/contact" class="btn-ghost">{{ pageContent('products', 'cta.button_secondary_text') }}</a></div></div></section>
@include('partials.footer')
@endsection
