@extends('layouts.app')
@section('title', 'Contact — AutoTerra')
@section('body')
@include('partials.nav')
<section style="background:var(--navy);padding:56px 60px;">
  <h1 style="font-size:34px;font-weight:800;color:#fff;margin-bottom:12px;">{!! pageContent('contact', 'hero.heading') !!}</h1>
  <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ pageContent('contact', 'hero.description') }}</p>
</section>
<section style="padding:56px 60px;max-width:800px;">
  <h2 style="font-size:18px;font-weight:800;margin-bottom:20px;">Contact form</h2>
  <p style="font-size:13px;color:var(--muted);margin-bottom:24px;">Form placeholder — integrate with your backend endpoint.</p>
</section>
@include('partials.footer')
@endsection
