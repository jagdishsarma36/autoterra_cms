@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' — AutoTerra')
@section('body')
@include('partials.nav')

@if($page->featured_image)
<img src="{{ $page->featured_image }}" alt="{{ $page->title }}" style="width:100%;max-height:400px;object-fit:cover;">
@endif

@php
  $blocks = $page->allBlocks();
  $blockTypes = \App\Models\PageContent::where('page', 'cms:' . $page->slug)->pluck('type', 'key')->toArray();
@endphp

@if(count($blocks) > 0)

  @php
    $consumedKeys = [];
    $knownPrefixes = ['hero.', 'stats', 'section.', 'features', 'features.', 'faq', 'faq.', 'testimonials', 'testimonial', 'testimonials.', 'form', 'cta.'];

    function isConsumedKey(string $key, array $prefixes): bool {
      foreach ($prefixes as $prefix) {
        if ($key === $prefix || str_starts_with($key, $prefix)) {
          return true;
        }
      }
      return false;
    }

    $remainingBlocks = [];
    foreach ($blocks as $key => $value) {
      if (isConsumedKey($key, $knownPrefixes)) {
        $consumedKeys[] = $key;
      } else {
        $remainingBlocks[$key] = $value;
      }
    }
  @endphp

  {{-- HERO SECTION --}}
  @if(isset($blocks['hero.heading']))
  <section style="background:var(--navy);padding:60px;">
    @if(isset($blocks['hero.pill_text']))
    <div class="sec-eye">{{ $blocks['hero.pill_text'] }}</div>
    @endif
    <h1 style="font-size:38px;font-weight:800;color:#fff;margin-bottom:14px;">{{ $blocks['hero.heading'] }}</h1>
    @if(isset($blocks['hero.description']))
    <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ $blocks['hero.description'] }}</p>
    @endif
    @if(isset($blocks['hero.button_primary_text']))
    <div style="margin-top:24px;">
      <a href="{{ $blocks['hero.button_primary_url'] ?? '#' }}" class="btn-cyan">{{ $blocks['hero.button_primary_text'] }}</a>
      @if(isset($blocks['hero.button_secondary_text']))
      <a href="{{ $blocks['hero.button_secondary_url'] ?? '#' }}" class="btn-ghost" style="margin-left:12px;">{{ $blocks['hero.button_secondary_text'] }}</a>
      @endif
    </div>
    @endif
  </section>
  @endif

  {{-- STATS STRIP --}}
  @if(isset($blocks['stats']))
  @php $stats = is_array($blocks['stats']) ? $blocks['stats'] : json_decode($blocks['stats'], true) ?? []; @endphp
  @if(count($stats) > 0)
  <section class="section section-white">
    <div style="display:flex;justify-content:center;gap:60px;flex-wrap:wrap;">
      @foreach($stats as $stat)
      <div style="text-align:center;">
        <div style="font-size:42px;font-weight:800;color:var(--cyan);">{{ $stat['number'] ?? '' }}</div>
        <div style="font-size:13px;color:var(--muted);margin-top:6px;">{{ $stat['label'] ?? '' }}</div>
      </div>
      @endforeach
    </div>
  </section>
  @endif
  @endif

  {{-- SECTION --}}
  @if(isset($blocks['section.heading']))
  <section class="section section-white">
    @if(isset($blocks['section.eyebrow']))
    <div class="sec-eye">{{ $blocks['section.eyebrow'] }}</div>
    @endif
    <h2 class="sec-h2">{{ $blocks['section.heading'] }}</h2>
    @if(isset($blocks['section.description']))
    <p class="sec-sub">{{ $blocks['section.description'] }}</p>
    @endif
  </section>
  @endif

  {{-- FEATURES --}}
  @if(isset($blocks['features']))
  @php $features = is_array($blocks['features']) ? $blocks['features'] : (json_decode($blocks['features'], true) ?? []); @endphp
  @if(count($features) > 0)
  <section class="section section-light">
    @if(isset($blocks['features.eyebrow']))
    <div class="sec-eye">{{ $blocks['features.eyebrow'] }}</div>
    @endif
    @if(isset($blocks['features.heading']))
    <h2 class="sec-h2">{{ $blocks['features.heading'] }}</h2>
    @endif
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:32px;">
      @foreach($features as $feature)
      <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
        @if(is_array($feature))
          <h3 style="font-size:16px;font-weight:800;margin-bottom:8px;">{{ $feature['title'] ?? '' }}</h3>
          <p style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $feature['description'] ?? '' }}</p>
        @else
          <p style="font-size:14px;color:var(--body);">{{ $feature }}</p>
        @endif
      </div>
      @endforeach
    </div>
  </section>
  @endif
  @endif

  {{-- FAQ --}}
  @if(isset($blocks['faq']))
  @php $faq = is_array($blocks['faq']) ? $blocks['faq'] : (json_decode($blocks['faq'], true) ?? []); @endphp
  @if(count($faq) > 0)
  <section class="section section-white">
    <div class="sec-eye">FAQ</div>
    <h2 class="sec-h2">{{ $blocks['faq.heading'] ?? 'Frequently Asked Questions' }}</h2>
    <div style="max-width:720px;margin-top:24px;">
      @foreach($faq as $item)
      <div style="border-bottom:1px solid var(--border);padding:18px 0;">
        <h3 style="font-size:15px;font-weight:700;color:var(--body);margin-bottom:8px;">{{ $item['question'] ?? '' }}</h3>
        <p style="font-size:14px;color:var(--muted);line-height:1.7;">{{ $item['answer'] ?? '' }}</p>
      </div>
      @endforeach
    </div>
  </section>
  @endif
  @endif

  {{-- TESTIMONIALS --}}
  @if(isset($blocks['testimonials']) || isset($blocks['testimonial']))
  @php $testRaw = $blocks['testimonials'] ?? $blocks['testimonial']; $testimonials = is_array($testRaw) ? $testRaw : (json_decode($testRaw, true) ?? []); @endphp
  @if(count($testimonials) > 0)
  <section class="section section-light">
    @if(isset($blocks['testimonials.eyebrow']))
    <div class="sec-eye">{{ $blocks['testimonials.eyebrow'] }}</div>
    @endif
    @if(isset($blocks['testimonials.heading']))
    <h2 class="sec-h2">{{ $blocks['testimonials.heading'] }}</h2>
    @endif
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:32px;">
      @foreach($testimonials as $item)
      <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;">
        <p style="font-size:14px;color:var(--body);line-height:1.7;font-style:italic;margin-bottom:16px;">"{{ $item['quote'] ?? '' }}"</p>
        <div style="font-size:13px;font-weight:700;color:var(--body);">{{ $item['author_name'] ?? '' }}</div>
        <div style="font-size:12px;color:var(--muted);">{{ $item['author_org'] ?? '' }}</div>
      </div>
      @endforeach
    </div>
  </section>
  @endif
  @endif

  {{-- EMBEDDED FORM --}}
  @if(isset($blocks['form']))
  @php
    $embeddedForm = \App\Models\FormCms::where('slug', $blocks['form'])->where('is_active', true)->first();
    $embeddedFields = $embeddedForm ? $embeddedForm->fields()->orderBy('sort_order')->get() : collect();
  @endphp
  @if($embeddedForm)
  <section class="section section-white" style="max-width:700px;margin:0 auto;">
    <h2 class="sec-h2">{{ $embeddedForm->name }}</h2>
    @if($embeddedForm->description)
    <p class="sec-sub">{{ $embeddedForm->description }}</p>
    @endif
    @if(session('form_success'))
    <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
      <i class="ti ti-check-circle" style="font-size:20px;color:#065F46;"></i>
      <span style="font-size:14px;color:#065F46;font-weight:600;">{{ session('form_success') }}</span>
    </div>
    @endif
    <form method="POST" action="{{ route('form.submit', $embeddedForm->slug) }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        @foreach($embeddedFields as $field)
        @php $span = $field->width === 100 ? 'grid-column: 1 / -1;' : ''; @endphp
        <div style="{{ $span }}">
          @if($field->type !== 'hidden')
          <label style="display:block;font-size:13px;font-weight:600;color:var(--body);margin-bottom:5px;">
            {{ $field->label }} @if($field->is_required)<span style="color:#EF4444;">*</span>@endif
          </label>
          @endif
          @if(in_array($field->type, ['text', 'email', 'number', 'phone', 'date', 'time']))
            <input type="{{ $field->type === 'phone' ? 'tel' : $field->type }}" name="field_{{ $field->name }}" placeholder="{{ $field->placeholder }}" value="{{ old('field_' . $field->name) }}" @if($field->is_required) required @endif style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;outline:none;">
          @endif
          @if($field->type === 'textarea')
            <textarea name="field_{{ $field->name }}" placeholder="{{ $field->placeholder }}" @if($field->is_required) required @endif rows="4" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;resize:vertical;outline:none;">{{ old('field_' . $field->name) }}</textarea>
          @endif
          @if($field->type === 'select')
            <select name="field_{{ $field->name }}" @if($field->is_required) required @endif style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);background:#fff;box-sizing:border-box;">
              <option value="">{{ $field->placeholder ?: 'Select...' }}</option>
              @foreach($field->options as $opt)<option value="{{ $opt }}" {{ old('field_' . $field->name) === $opt ? 'selected' : '' }}>{{ $opt }}</option>@endforeach
            </select>
          @endif
          @if($field->help_text)
          <p style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $field->help_text }}</p>
          @endif
        </div>
        @endforeach
      </div>
      <div style="margin-top:24px;">
        <button type="submit" style="width:100%;padding:13px 0;background:var(--cyan);color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;">{{ $embeddedForm->submit_button_text }}</button>
      </div>
    </form>
  </section>
  @endif
  @endif

  {{-- CTA BAND --}}
  @if(isset($blocks['cta.heading']))
  <section class="cta-band">
    <div class="cta-band-inner">
      <h2>{{ $blocks['cta.heading'] }}</h2>
      @if(isset($blocks['cta.description']))
      <p>{{ $blocks['cta.description'] }}</p>
      @endif
      <div class="cta-row">
        @if(isset($blocks['cta.button_primary_text']))
        <a href="{{ $blocks['cta.button_primary_url'] ?? '/buy' }}" class="btn-cyan">{{ $blocks['cta.button_primary_text'] }}</a>
        @endif
        @if(isset($blocks['cta.button_secondary_text']))
        <a href="{{ $blocks['cta.button_secondary_url'] ?? '/contact' }}" class="btn-ghost">{{ $blocks['cta.button_secondary_text'] }}</a>
        @endif
      </div>
    </div>
  </section>
  @endif

  {{-- REMAINING BLOCKS (any blocks not rendered by the named sections above) --}}
  @if(count($remainingBlocks) > 0)
  @foreach($remainingBlocks as $rKey => $rValue)
    @php $rType = $blockTypes[$rKey] ?? 'text'; @endphp
    @if($rType === 'html')
      {!! $rValue !!}
    @elseif($rType === 'html_inline' || $rType === 'richtext')
      <div class="page-content">{!! $rValue !!}</div>
    @elseif(str_starts_with($rType, 'html_section'))
    @php $sectionClass = substr($rType, 13) ?: 'section-white'; @endphp
    <section class="section {{ $sectionClass }}">
      {!! $rValue !!}
    </section>
    @elseif(is_array($rValue))
    <section class="section section-white">
      <div style="max-width:860px;margin:0 auto;">
        @foreach($rValue as $item)
          @if(is_array($item))
          <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:28px;margin-bottom:16px;">
            @foreach($item as $k => $v)
            <div style="margin-bottom:6px;">
              <strong style="font-size:13px;color:var(--body);">{{ ucfirst(str_replace('_', ' ', $k)) }}:</strong>
              <span style="font-size:13px;color:var(--muted);">{{ $v }}</span>
            </div>
            @endforeach
          </div>
          @else
          <p style="font-size:15px;color:var(--body);line-height:1.7;margin-bottom:8px;">{{ $item }}</p>
          @endif
        @endforeach
      </div>
    </section>
    @else
    <section class="section section-white">
      <div style="max-width:860px;margin:0 auto;">
        <div class="page-content">{!! $rValue !!}</div>
      </div>
    </section>
    @endif
  @endforeach
  @endif

@else
  {{-- No blocks at all — render page title + raw content --}}
  <section style="background:var(--navy);padding:60px;">
    <h1 style="font-size:38px;font-weight:800;color:#fff;margin-bottom:14px;">{{ $page->title }}</h1>
    @if($page->excerpt)
    <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ $page->excerpt }}</p>
    @endif
  </section>
  @if($page->content)
  <section style="max-width:800px;margin:0 auto;padding:48px 60px;">
    <div class="page-content" style="font-size:16px;color:var(--body);line-height:1.8;">
      {!! $page->content !!}
    </div>
  </section>
  @endif
@endif

@include('partials.footer')
@endsection

@section('styles')
<style>
  .page-content h2 { font-size: 24px; font-weight: 800; color: var(--body); margin: 32px 0 16px; }
  .page-content h3 { font-size: 20px; font-weight: 700; color: var(--body); margin: 28px 0 12px; }
  .page-content p { margin-bottom: 16px; }
  .page-content ul, .page-content ol { margin-bottom: 16px; padding-left: 24px; }
  .page-content li { margin-bottom: 6px; }
  .page-content blockquote { border-left: 3px solid var(--cyan); padding: 12px 20px; background: var(--off); border-radius: 0 8px 8px 0; margin: 20px 0; }
  .page-content a { color: var(--cyan); text-decoration: underline; }
  .page-content img { max-width: 100%; border-radius: 8px; margin: 16px 0; }
</style>
@endsection
