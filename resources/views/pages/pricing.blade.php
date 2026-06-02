@extends('layouts.app')
@section('title', 'Pricing — AutoTerra')
@section('body')
@include('partials.nav')
<section style="background:var(--navy);padding:64px 60px;text-align:center;border-bottom:1px solid rgba(0,168,248,0.10);">
  <div class="sec-eye" style="text-align:center;">{{ pageContent('pricing', 'hero.eyebrow') }}</div>
  <h1 style="font-size:36px;font-weight:800;color:#fff;letter-spacing:-0.7px;line-height:1.2;margin-bottom:14px;">{!! pageContent('pricing', 'hero.heading') !!}</h1>
  <p style="font-size:15px;color:rgba(210,230,248,0.50);line-height:1.75;max-width:540px;margin:0 auto;">{{ pageContent('pricing', 'hero.description') }}</p>
  <div style="background:rgba(0,168,248,0.08);border:1px solid rgba(0,168,248,0.20);border-radius:8px;padding:12px 20px;display:inline-flex;align-items:center;gap:10px;font-size:13px;color:rgba(210,230,248,0.65);max-width:600px;margin:24px auto 0;">
    <i class="ti ti-world" style="font-size:18px;color:var(--cyan);"></i>
    <span>{{ pageContent('pricing', 'hero.region_note') }}</span>
  </div>
</section>
<section style="background:var(--off);padding:56px 60px;">
  <div class="sec-eye">Questions</div>
  <h2 class="sec-h2" style="margin-bottom:24px;">Common pricing questions</h2>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    @foreach(pageContentJson('pricing', 'faq') as $faq)
    <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:22px;">
      <h4 style="font-size:14px;font-weight:700;color:var(--body);margin-bottom:8px;display:flex;align-items:center;gap:8px;">
        <i class="ti ti-help-circle" style="color:var(--cyan);font-size:16px;"></i>
        {{ $faq['question'] }}
      </h4>
      <p style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $faq['answer'] }}</p>
    </div>
    @endforeach
  </div>
</section>
<div style="background:var(--navy);padding:56px 60px;display:flex;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;">
  <div>
    <h3 style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.3px;margin-bottom:8px;">{{ pageContent('pricing', 'enterprise.heading') }}</h3>
    <p style="font-size:14px;color:rgba(210,230,248,0.45);max-width:500px;line-height:1.7;">{{ pageContent('pricing', 'enterprise.description') }}</p>
  </div>
  <div style="display:flex;gap:12px;flex-wrap:wrap;">
    <a href="/contact" class="btn-cyan">{{ pageContent('pricing', 'enterprise.button_primary_text') }}</a>
    <a href="/products" class="btn-ghost">{{ pageContent('pricing', 'enterprise.button_secondary_text') }}</a>
  </div>
</div>
@include('partials.footer')
@endsection
