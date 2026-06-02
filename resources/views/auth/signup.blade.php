@extends('layouts.guest')
@section('title', 'Create Account — AutoTerra')

@section('body')
<html lang="en">
<head>
  <style>
    html, body { height: 100%; }
    body { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
    .signup-left { background: var(--navy); position: relative; display: flex; flex-direction: column; justify-content: center; padding: 48px 52px; overflow: hidden; }
    .signup-left-bg { position: absolute; inset: 0; z-index: 0; }
    .signup-left-bg::before { content: ''; position: absolute; width: 500px; height: 500px; background: radial-gradient(circle, rgba(0,168,248,0.12) 0%, transparent 70%); top: -100px; right: -80px; }
    .signup-left-bg::after { content: ''; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, rgba(8,96,176,0.16) 0%, transparent 70%); bottom: 60px; left: -40px; }
    .signup-left-inner { position: relative; z-index: 1; }
    .signup-left-logo { display: flex; align-items: baseline; text-decoration: none; margin-bottom: 40px; }
    .signup-left-logo .logo-a { color: #fff; font-size: 22px; font-weight: 800; }
    .signup-left-logo .logo-t { color: var(--cyan); font-size: 22px; font-weight: 800; }
    .signup-tagline { font-size: 26px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -0.5px; margin-bottom: 14px; }
    .signup-tagline span { color: var(--cyan); }
    .signup-tagline-sub { font-size: 14px; color: rgba(210,230,248,0.45); line-height: 1.75; max-width: 320px; margin-bottom: 36px; }
    .signup-perks { display: flex; flex-direction: column; gap: 12px; }
    .signup-perk { display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: rgba(210,230,248,0.65); line-height: 1.5; }
    .signup-perk-icon { width: 28px; height: 28px; flex-shrink: 0; background: rgba(0,168,248,0.12); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--cyan); font-size: 14px; margin-top: 1px; }
    .signup-right { background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding: 40px 60px; overflow-y: auto; }
    .signup-card { width: 100%; max-width: 420px; }
    .signup-card-head { margin-bottom: 24px; margin-top: 8px; }
    .signup-card-head h1 { font-size: 24px; font-weight: 800; color: var(--body); letter-spacing: -0.4px; margin-bottom: 6px; }
    .signup-card-head p { font-size: 14px; color: var(--muted); }
    .social-btns { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
    .btn-social { width: 100%; background: #fff; color: var(--body); border: 1.5px solid var(--border); border-radius: 8px; padding: 11px 16px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: border-color 0.15s, box-shadow 0.15s; text-decoration: none; }
    .btn-social:hover { border-color: #aaa; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
    .btn-social .social-icon { width: 20px; height: 20px; flex-shrink: 0; }
    .signup-divider { display: flex; align-items: center; gap: 12px; margin: 4px 0 18px; }
    .signup-divider::before, .signup-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .signup-divider span { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--body); margin-bottom: 5px; }
    .form-group label .opt-tag { font-size: 11px; font-weight: 400; color: var(--muted); margin-left: 4px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 7px; font-family: inherit; font-size: 14px; color: var(--body); background: #fff; transition: border-color 0.15s, box-shadow 0.15s; outline: none; box-sizing: border-box; }
    .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0,168,248,0.12); }
    .form-control::placeholder { color: #A0B4C5; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .field-icon-wrap { position: relative; }
    .field-icon-wrap .form-control { padding-right: 40px; }
    .field-icon-wrap .field-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 18px; cursor: pointer; transition: color 0.15s; }
    .field-icon-wrap .field-icon:hover { color: var(--cyan); }
    .optional-toggle { font-size: 13px; color: var(--cyan); font-weight: 600; cursor: pointer; margin-bottom: 16px; display: inline-flex; align-items: center; gap: 5px; user-select: none; }
    .optional-toggle:hover { text-decoration: underline; }
    #optionalFields { display: none; }
    #optionalFields.open { display: block; }
    .pw-strength-bar { height: 4px; border-radius: 2px; margin-top: 6px; background: var(--border); overflow: hidden; }
    .pw-strength-fill { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0%; }
    .pw-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
    .terms-check { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--muted); margin-bottom: 18px; line-height: 1.5; }
    .terms-check input[type="checkbox"] { width: 15px; height: 15px; flex-shrink: 0; margin-top: 2px; accent-color: var(--cyan); cursor: pointer; }
    .terms-check a { color: var(--cyan); text-decoration: none; font-weight: 600; }
    .terms-check a:hover { text-decoration: underline; }
    .btn-signup { width: 100%; background: var(--cyan); color: #fff; border: none; border-radius: 7px; padding: 13px 0; font-size: 14px; font-weight: 700; font-family: inherit; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 18px; }
    .btn-signup:hover { background: var(--cyan-dk); }
    .btn-signup:disabled { opacity: 0.6; cursor: not-allowed; }
    .signup-alert { background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 7px; padding: 10px 14px; font-size: 13px; color: #B91C1C; display: none; align-items: center; gap: 8px; margin-bottom: 14px; }
    .signup-alert.show { display: flex; }
    .signup-signin { text-align: center; font-size: 13px; color: var(--muted); margin-bottom: 6px; }
    .signup-signin a { color: var(--cyan); font-weight: 700; text-decoration: none; }
    .signup-signin a:hover { text-decoration: underline; }
    .signup-back { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 16px; font-size: 12px; color: var(--muted); text-decoration: none; transition: color 0.15s; }
    .signup-back:hover { color: var(--cyan); }
    @media (max-width: 900px) { body { grid-template-columns: 1fr; } .signup-left { display: none; } .signup-right { padding: 36px 24px; min-height: 100vh; } }
    @media (max-width: 600px) { .signup-right { padding: 28px 18px; } .form-row-2 { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<div class="signup-left">
  <div class="signup-left-bg"></div>
  <div class="signup-left-inner">
    <a href="/" class="signup-left-logo">
      <span class="logo-a">AUTO</span><span class="logo-t">TERRA</span>
    </a>
    <h2 class="signup-tagline">{!! pageContent('signup', 'tagline') !!}</h2>
    <p class="signup-tagline-sub">{{ pageContent('signup', 'tagline_sub') }}</p>
    <div class="signup-perks">
      @foreach(pageContentJson('signup', 'perks') as $perk)
      <div class="signup-perk">
        <div class="signup-perk-icon"><i class="ti ti-check"></i></div>
        {{ $perk }}
      </div>
      @endforeach
    </div>
  </div>
</div>

<div class="signup-right">
  <div class="signup-card">
    <div class="signup-card-head">
      <h1>Create your account</h1>
      <p>Sign up with Google, Microsoft, or your email address.</p>
    </div>

    @if($errors->any())
    <div class="signup-alert show">
      <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i>
      <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <div class="social-btns">
      <a href="{{ route('social.redirect', 'google') }}" class="btn-social">
        <svg class="social-icon" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        Continue with Google
      </a>
      <a href="{{ route('social.redirect', 'microsoft') }}" class="btn-social">
        <svg class="social-icon" viewBox="0 0 24 24"><rect x="1" y="1" width="10" height="10" fill="#F25022"/><rect x="13" y="1" width="10" height="10" fill="#7FBA00"/><rect x="1" y="13" width="10" height="10" fill="#00A4EF"/><rect x="13" y="13" width="10" height="10" fill="#FFB900"/></svg>
        Continue with Microsoft
      </a>
    </div>

    <div class="signup-divider"><span>or register with email</span></div>

    <form method="POST" action="{{ route('register') }}" id="signupForm" novalidate>
      @csrf
      @if($redirect ?? null)
      <input type="hidden" name="redirect" value="{{ $redirect }}">
      @endif
      <div class="form-row-2">
        <div class="form-group">
          <label for="firstName">First name</label>
          <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Ravi" value="{{ old('firstName') }}" required>
        </div>
        <div class="form-group">
          <label for="lastName">Last name</label>
          <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Kumar" value="{{ old('lastName') }}" required>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Work email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@company.com" autocomplete="email" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label for="password">Password <span class="opt-tag">min 8 characters</span></label>
        <div class="field-icon-wrap">
          <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" autocomplete="new-password" required minlength="8" oninput="checkPasswordStrength(this.value)">
          <i class="ti ti-eye field-icon" id="pwToggle" onclick="togglePw()" title="Show password"></i>
        </div>
        <div class="pw-strength-bar"><div class="pw-strength-fill" id="pwFill"></div></div>
        <div class="pw-hint" id="pwHint">Use 8+ characters with a mix of letters, numbers, and symbols.</div>
      </div>

      <div class="form-group">
        <label for="password_confirmation">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm your password" autocomplete="new-password" required>
      </div>

      <div class="optional-toggle" onclick="toggleOptional()" id="optToggle">
        <i class="ti ti-chevron-right" id="optIcon" style="transition:transform 0.2s;"></i>
        Add organisation details <span class="opt-tag" style="margin-left:2px;">(optional)</span>
      </div>
      <div id="optionalFields">
        <div class="form-group">
          <label for="company">Company / organisation <span class="opt-tag">optional</span></label>
          <input type="text" id="company" name="company" class="form-control" placeholder="Surveying Co. Pvt Ltd" value="{{ old('company') }}">
        </div>
        <div class="form-group">
          <label for="phone">Phone number <span class="opt-tag">optional</span></label>
          <input type="tel" id="phone" name="phone" class="form-control" placeholder="+91 98xxxxxxxx" value="{{ old('phone') }}">
        </div>
        <div class="form-group">
          <label for="address">Address <span class="opt-tag">optional</span></label>
          <input type="text" id="address" name="address" class="form-control" placeholder="City, State, Country" value="{{ old('address') }}">
        </div>
      </div>

      <div class="terms-check">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">
          I agree to the <a href="/terms" target="_blank">Terms of Use</a>,
          <a href="/privacy" target="_blank">Privacy Policy</a>, and
          <a href="/eula" target="_blank">EULA</a>.
        </label>
      </div>

      <button type="submit" class="btn-login" id="signupBtn">
        <i class="ti ti-user-plus"></i> Create account &amp; start free trial
      </button>
    </form>

    <div class="signup-signin">
      Already have an account? <a href="{{ route('login') }}{{ isset($redirect) && $redirect ? '?redirect=' . urlencode($redirect) : '' }}">Sign in</a>
    </div>

    <a href="/" class="signup-back">
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
function checkPasswordStrength(pw) {
  var fill = document.getElementById('pwFill');
  var hint = document.getElementById('pwHint');
  var score = 0;
  if (pw.length >= 8) score++;
  if (pw.length >= 12) score++;
  if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
  if (/\d/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  var pct = ['0%','20%','40%','60%','80%','100%'][score];
  var color = ['#D1D5DB','#EF4444','#F59E0B','#F59E0B','#10B981','#10B981'][score];
  var label = ['','Weak','Fair','Fair','Strong','Very strong'][score];
  fill.style.width = pct;
  fill.style.background = color;
  hint.textContent = pw.length === 0 ? 'Use 8+ characters with a mix of letters, numbers, and symbols.' : label;
}
function toggleOptional() {
  var panel = document.getElementById('optionalFields');
  var icon = document.getElementById('optIcon');
  var open = panel.classList.toggle('open');
  icon.style.transform = open ? 'rotate(90deg)' : 'rotate(0deg)';
}
</script>
</body>
</html>
@endsection
