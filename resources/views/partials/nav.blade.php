@php
  $logoA = \App\Models\Setting::get('header_logo_a', 'AUTO');
  $logoT = \App\Models\Setting::get('header_logo_t', 'TERRA');
  $logoImage = \App\Models\Setting::get('header_logo_image', '');
  $navLinks = json_decode(\App\Models\Setting::get('header_nav_links', '[{"label":"Products","url":"/products"},{"label":"Solutions","url":"/solutions"},{"label":"Resources","url":"/resources"},{"label":"Pricing","url":"/pricing"},{"label":"Blog","url":"/blog"}]'), true);
  $loginText = \App\Models\Setting::get('header_login_text', 'Login');
  $ctaText = \App\Models\Setting::get('header_cta_text', 'Buy Now');
  $ctaUrl = \App\Models\Setting::get('header_cta_url', '/buy');
  $isLoggedIn = auth()->check();
@endphp
<nav class="nav">
  <a href="/" class="nav-logo">
    @if($logoImage)
      <img src="{{ $logoImage }}" alt="{{ $logoA }}{{ $logoT }}" style="height:28px;width:auto;">
    @else
      <span class="logo-a">{{ $logoA }}</span><span class="logo-t">{{ $logoT }}</span>
    @endif
  </a>
  {{--<div class="nav-links">
    @foreach($navLinks as $link)
    <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
    @endforeach
    @if($isLoggedIn)
    <a href="{{ route('dashboard') }}">Dashboard</a>
    @endif
  </div>--}}
  <div class="nav-links">
    @foreach($navLinks as $link)
    <a href="{{ $link['url'] }}"
       class="{{ request()->path() == trim($link['url'], '/') ? 'active' : '' }}">
        {{ $link['label'] }}
    </a>
    @endforeach

    @if($isLoggedIn)
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
    @endif
  </div>
  <div class="nav-right">
    @if($isLoggedIn)
      <a href="{{ route('dashboard') }}" class="nav-login nav-das"><i class="ti ti-layout-dashboard" style="font-size:15px;"></i> Dashboard</a>
    @else
      <a href="{{ route('login') }}" class="nav-login"><i class="ti ti-user" style="font-size:15px;"></i> {{ $loginText }}</a>
    @endif
    <a href="{{ $ctaUrl }}" class="btn-cyan" style="padding:10px 22px;font-size:13px;font-weight:700;border-radius:7px;">{{ $ctaText }}</a>
    <button class="hamburger" aria-label="Open menu">
      <i class="ti ti-menu-2"></i>
    </button>
  </div>
</nav>
