@extends('layouts.guest')
@section('title', 'Forgot Password — AutoTerra')

@section('body')
<html lang="en">
<head>
  <style>
    html, body { height: 100%; }
    body { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
    .login-left { background: var(--navy); position: relative; display: flex; flex-direction: column; justify-content: space-between; padding: 40px 52px; overflow: hidden; }
    .login-left-bg { position: absolute; inset: 0; z-index: 0; }
    .login-left-bg::before { content: ''; position: absolute; width: 480px; height: 480px; background: radial-gradient(circle, rgba(0,168,248,0.14) 0%, transparent 70%); top: -80px; left: -80px; }
    .login-left-bg::after { content: ''; position: absolute; width: 360px; height: 360px; background: radial-gradient(circle, rgba(8,96,176,0.20) 0%, transparent 70%); bottom: 40px; right: -60px; }
    .login-left-top { position: relative; z-index: 1; }
    .login-left-logo { display: flex; align-items: baseline; margin-bottom: 48px; text-decoration: none; }
    .login-left-logo .logo-a { color: #fff; font-size: 22px; font-weight: 800; }
    .login-left-logo .logo-t { color: var(--cyan); font-size: 22px; font-weight: 800; }
    .login-tagline { font-size: 28px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -0.5px; margin-bottom: 18px; }
    .login-tagline span { color: var(--cyan); }
    .login-tagline-sub { font-size: 14px; color: rgba(210,230,248,0.45); line-height: 1.75; max-width: 320px; }
    .login-features { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 14px; margin-top: 40px; }
    .login-feat { display: flex; align-items: center; gap: 12px; font-size: 13px; color: rgba(210,230,248,0.60); }
    .login-feat i { width: 30px; height: 30px; background: rgba(0,168,248,0.12); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--cyan); font-size: 16px; flex-shrink: 0; }
    .login-left-bottom { position: relative; z-index: 1; }
    .login-footer-note { font-size: 11px; color: rgba(210,230,248,0.22); }
    .login-footer-note a { color: rgba(210,230,248,0.28); }
    .login-right { background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 60px; overflow-y: auto; }
    .login-card { width: 100%; max-width: 400px; }
    .login-card-head { margin-bottom: 28px; }
    .login-card-head h1 { font-size: 24px; font-weight: 800; color: var(--body); letter-spacing: -0.4px; margin-bottom: 6px; }
    .login-card-head p { font-size: 14px; color: var(--muted); }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--body); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 11px 14px; border: 1px solid var(--border); border-radius: 7px; font-family: inherit; font-size: 14px; color: var(--body); background: #fff; transition: border-color 0.15s, box-shadow 0.15s; outline: none; box-sizing: border-box; }
    .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0,168,248,0.12); }
    .form-control::placeholder { color: #A0B4C5; }
    .btn-login { width: 100%; background: var(--cyan); color: #fff; border: none; border-radius: 7px; padding: 13px 0; font-size: 14px; font-weight: 700; font-family: inherit; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 22px; }
    .btn-login:hover { background: var(--cyan-dk); }
    .btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
    .login-alert { background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 7px; padding: 10px 14px; font-size: 13px; color: #B91C1C; display: none; align-items: center; gap: 8px; margin-bottom: 16px; }
    .login-alert.show { display: flex; }
    .login-success { background: #F0FFF8; border: 1px solid #6EE7B7; border-radius: 7px; padding: 10px 14px; font-size: 13px; color: #065F46; display: none; align-items: center; gap: 8px; margin-bottom: 16px; }
    .login-success.show { display: flex; }
    .login-signup { text-align: center; font-size: 13px; color: var(--muted); margin-bottom: 6px; }
    .login-signup a { color: var(--cyan); font-weight: 700; text-decoration: none; }
    .login-signup a:hover { text-decoration: underline; }
    .login-back { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 20px; font-size: 12px; color: var(--muted); text-decoration: none; transition: color 0.15s; }
    .login-back:hover { color: var(--cyan); }
    @media (max-width: 900px) { body { grid-template-columns: 1fr; } .login-left { display: none; } .login-right { padding: 40px 24px; min-height: 100vh; } }
  </style>
</head>
<body>

<div class="login-left">
  <div class="login-left-bg"></div>
  <div class="login-left-top">
    <a href="/" class="login-left-logo">
      <span class="logo-a">AUTO</span><span class="logo-t">TERRA</span>
    </a>
    <h2 class="login-tagline">{!! pageContent('login', 'tagline') !!}</h2>
    <p class="login-tagline-sub">{{ pageContent('login', 'tagline_sub') }}</p>
    <div class="login-features">
      @foreach(pageContentJson('login', 'features') as $feature)
      <div class="login-feat"><i class="ti ti-check"></i> {{ $feature }}</div>
      @endforeach
    </div>
  </div>
  <div class="login-left-bottom">
    <p class="login-footer-note">
      &copy; 2026 Infyterra Technologies &middot;
      <a href="/privacy">Privacy Policy</a> &middot;
      <a href="/terms">Terms</a>
    </p>
  </div>
</div>

<div class="login-right">
  <div class="login-card">
    <div class="login-card-head">
      <h1>Forgot password?</h1>
      <p>Enter your email address and we'll send you a new password.</p>
    </div>

    @if($errors->has('email'))
    <div class="login-alert show">
      <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i>
      <span>{{ $errors->first('email') }}</span>
    </div>
    @endif

    @if(session('success'))
    <div class="login-success show">
      <i class="ti ti-check-circle" style="font-size:16px;flex-shrink:0;"></i>
      <span>{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('password.forgot') }}">
      @csrf
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@company.com" autocomplete="email" value="{{ old('email') }}" required>
      </div>

      <button type="submit" class="btn-login">
        <i class="ti ti-mail"></i> Send New Password
      </button>
    </form>

    <div class="login-signup">
      Remember your password? <a href="{{ route('login') }}">Sign in</a>
    </div>

    <a href="/" class="login-back">
      <i class="ti ti-arrow-left"></i> Back to autoterra.net
    </a>
  </div>
</div>

</body>
</html>
@endsection
