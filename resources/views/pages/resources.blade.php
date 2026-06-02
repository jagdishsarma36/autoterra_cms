@extends('layouts.app')
@section('title', 'AutoTerra')
@section('body')
@include('partials.nav')
<section style="background:var(--navy);padding:60px;"><h1 style="font-size:34px;font-weight:800;color:#fff;">{{ ucfirst(str_replace('-', ' ', request()->path())) }}</h1><p style="font-size:15px;color:rgba(210,230,248,0.5);margin-top:12px;">This page is under development.</p></section>
@include('partials.footer')
@endsection
