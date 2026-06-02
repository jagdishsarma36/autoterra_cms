@extends('layouts.guest')
@section('title', 'Sign In — AutoTerra')

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
    .social-btns { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
    .btn-social { width: 100%; background: #fff; color: var(--body); border: 1.5px solid var(--border); border-radius: 8px; padding: 11px 16px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: border-color 0.15s, box-shadow 0.15s; text-decoration: none; }
    .btn-social:hover { border-color: #aaa; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
    .btn-social .social-icon { width: 20px; height: 20px; flex-shrink: 0; }
    .login-divider { display: flex; align-items: center; gap: 12px; margin: 4px 0 18px; }
    .login-divider::before, .login-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .login-divider span { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--body); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 11px 14px; border: 1px solid var(--border); border-radius: 7px; font-family: inherit; font-size: 14px; color: var(--body); background: #fff; transition: border-color 0.15s, box-shadow 0.15s; outline: none; box-sizing: border-box; }
    .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0,168,248,0.12); }
    .form-control::placeholder { color: #A0B4C5; }
    .field-icon-wrap { position: relative; }
    .field-icon-wrap .form-control { padding-right: 40px; }
    .field-icon-wrap .field-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 18px; cursor: pointer; transition: color 0.15s; }
    .field-icon-wrap .field-icon:hover { color: var(--cyan); }
    .form-row-inline { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .checkbox-wrap { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--body); cursor: pointer; }
    .checkbox-wrap input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--cyan); cursor: pointer; }
    .forgot-link { font-size: 13px; color: var(--cyan); font-weight: 600; text-decoration: none; transition: color 0.15s; }
    .forgot-link:hover { color: var(--cyan-dk); text-decoration: underline; }
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
      © 2026 Infyterra Technologies ·
      <a href="/privacy">Privacy Policy</a> ·
      <a href="/terms">Terms</a>
    </p>
  </div>
</div>

<div class="login-right">
  <div class="login-card">
    <div class="login-card-head">
      <a href="/" id="mobileLogoWrap" style="display:none;align-items:baseline;text-decoration:none;margin-bottom:24px;">
        <span style="font-size:18px;font-weight:800;color:var(--body);">AUTO</span><span style="font-size:18px;font-weight:800;color:var(--cyan);">TERRA</span>
      </a>
      <h1>Sign in</h1>
      <p>Welcome back. Choose how you'd like to sign in.</p>
    </div>

    @if($errors->has('social'))
    <div class="login-alert show">
      <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i>
      <span>{{ $errors->first('social') }}</span>
    </div>
    @endif

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

    <div class="social-btns">
      <a href="{{ route('social.redirect', 'google') }}" class="btn-social">
        <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
      </a>
      <a href="{{ route('social.redirect', 'microsoft') }}" class="btn-social">
        <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <rect x="1" y="1" width="10" height="10" fill="#F25022"/>
          <rect x="13" y="1" width="10" height="10" fill="#7FBA00"/>
          <rect x="1" y="13" width="10" height="10" fill="#00A4EF"/>
          <rect x="13" y="13" width="10" height="10" fill="#FFB900"/>
        </svg>
        Continue with Microsoft
      </a>
    </div>

    <div class="login-divider"><span>or sign in with email</span></div>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      @if($redirect ?? null)
      <input type="hidden" name="redirect" value="{{ $redirect }}">
      @endif
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@company.com" autocomplete="email" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="field-icon-wrap">
          <input type="password" id="password" name="password" class="form-control" placeholder="Your password" autocomplete="current-password" required>
          <i class="ti ti-eye field-icon" id="pwToggle" onclick="togglePw()" title="Show password"></i>
        </div>
      </div>

      <div class="form-row-inline">
        <label class="checkbox-wrap">
          <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Keep me signed in
        </label>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="btn-login">
        <i class="ti ti-login"></i> Sign in
      </button>
    </form>

    <div class="login-signup">
      Don't have an account? <a href="{{ route('register') }}{{ isset($redirect) && $redirect ? '?redirect=' . urlencode($redirect) : '' }}">Create one — it's free</a>
    </div>

    <a href="/" class="login-back">
      <i class="ti ti-arrow-left"></i> Back to autoterra.net
    </a>
  </div>
</div>

<script>
function togglePw() {
  var pw = document.getElementById('password');
  var icon = document.getElementById('pwToggle');
  var showing = pw.type === 'text';
  pw.type = showing ? 'password' : 'text';
  icon.classList.replace(showing ? 'ti-eye-off' : 'ti-eye', showing ? 'ti-eye' : 'ti-eye-off');
}
</script>
</body>
</html>
@endsection
