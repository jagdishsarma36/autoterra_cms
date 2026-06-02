@extends('layouts.app')
@section('title', 'Request a Quote — AutoTerra')
@section('body')
@include('partials.nav')
<section style="background:var(--navy);padding:60px;" class="qt-hero">
  <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(0,168,248,0.12);border:1px solid rgba(0,168,248,0.28);border-radius:30px;padding:5px 14px;font-size:12px;font-weight:700;color:var(--cyan);text-transform:uppercase;margin-bottom:18px;">{{ pageContent('quote', 'hero.badge_text') }}</div>
  <h1 style="font-size:36px;font-weight:800;color:#fff;margin-bottom:14px;">{!! pageContent('quote', 'hero.heading') !!}</h1>
  <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ pageContent('quote', 'hero.description') }}</p>

  <div class="qt-trust">
     @foreach(pageContentJson('quote', 'hero.trust_items') as $item)
      <div class="qt-trust-item"><span class="ti ti-shield-check"></span>{{ $item }}</div>
    @endforeach
  </div>

</section>
<section style="padding:56px 60px;max-width:800px;">
  <h2 style="font-size:18px;font-weight:800;margin-bottom:20px;">Quote request form</h2>
  <p style="font-size:13px;color:var(--muted);margin-bottom:24px;">Form placeholder — integrate with POST /api/quote endpoint.</p>
</section>
@include('partials.footer')
@endsection
