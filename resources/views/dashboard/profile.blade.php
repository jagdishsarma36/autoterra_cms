@extends('layouts.app')
@section('title', 'Profile — AutoTerra')
@section('body')
@include('partials.nav')
<div class="profile_main" style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div class="profile_tabs" style="flex:1;min-width:0;">
  <h1 style="font-size:28px;font-weight:800;margin-bottom:24px;">My Profile</h1>
  @if(session('success'))
  <div style="background:#F0FFF8;border:1px solid #6EE7B7;border-radius:8px;padding:12px 18px;margin-bottom:24px;font-size:14px;color:#065F46;">{{ session('success') }}</div>
  @endif
  <form method="POST" action="{{ route('dashboard.profile.update') }}">
    @csrf
    @method('PUT')
    <div style="margin-bottom:16px;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Name</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;" required>
    </div>
    <div style="margin-bottom:16px;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Email</label>
      <input type="email" value="{{ $user->email }}" disabled style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;background:var(--off);color:var(--muted);">
    </div>
    <div style="margin-bottom:16px;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Company</label>
      <input type="text" name="company" value="{{ old('company', $user->company) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;">
    </div>
    <div style="margin-bottom:16px;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Phone</label>
      <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;">
    </div>
    <div style="margin-bottom:16px;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Address</label>
      <input type="text" name="address" value="{{ old('address', $user->address) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;">
    </div>
    <button type="submit" style="background:var(--cyan);color:#fff;border:none;border-radius:7px;padding:12px 28px;font-size:14px;font-weight:700;cursor:pointer;">Save Changes</button>
  </form>

  <hr style="margin:32px 0;border:none;border-top:1px solid var(--border);">

  <h2 style="font-size:22px;font-weight:700;margin-bottom:20px;">Change Password</h2>
  @if(session('password_success'))
  <div style="background:#F0FFF8;border:1px solid #6EE7B7;border-radius:8px;padding:12px 18px;margin-bottom:24px;font-size:14px;color:#065F46;">{{ session('password_success') }}</div>
  @endif
  @if(session('password_error'))
  <div style="background:#FFF0F0;border:1px solid #FCA5A5;border-radius:8px;padding:12px 18px;margin-bottom:24px;font-size:14px;color:#991B1B;">{{ session('password_error') }}</div>
  @endif
  <form method="POST" action="{{ route('dashboard.password.update') }}">
    @csrf
    <div style="margin-bottom:16px;position:relative;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Current Password</label>
      <input type="password" name="current_password" id="current_password" style="width:100%;padding:10px 40px 10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;" required>
      <span onclick="togglePassword('current_password', this)" style="position:absolute;right:12px;bottom:10px;cursor:pointer;color:var(--muted);"><i class="ti ti-eye"></i></span>
    </div>
    <div style="margin-bottom:16px;position:relative;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">New Password</label>
      <input type="password" name="new_password" id="new_password" style="width:100%;padding:10px 40px 10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;" required>
      <span onclick="togglePassword('new_password', this)" style="position:absolute;right:12px;bottom:10px;cursor:pointer;color:var(--muted);"><i class="ti ti-eye"></i></span>
    </div>
    <div style="margin-bottom:16px;position:relative;">
      <label style="display:block;font-size:13px;font-weight:600;margin-bottom:5px;">Confirm New Password</label>
      <input type="password" name="new_password_confirmation" id="new_password_confirmation" style="width:100%;padding:10px 40px 10px 14px;border:1px solid var(--border);border-radius:7px;font-size:14px;" required>
      <span onclick="togglePassword('new_password_confirmation', this)" style="position:absolute;right:12px;bottom:10px;cursor:pointer;color:var(--muted);"><i class="ti ti-eye"></i></span>
    </div>
    <button type="submit" style="background:var(--cyan);color:#fff;border:none;border-radius:7px;padding:12px 28px;font-size:14px;font-weight:700;cursor:pointer;">Update Password</button>
  </form>
</div>
</div>

@include('partials.footer')
@endsection

@section('scripts')
<script>
function togglePassword(id, el) {
  const input = document.getElementById(id);
  const icon = el.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'ti ti-eye-off';
  } else {
    input.type = 'password';
    icon.className = 'ti ti-eye';
  }
}
</script>
@endsection
