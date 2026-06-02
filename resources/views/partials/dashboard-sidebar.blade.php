@php
  $currentRoute = request()->route()?->getName() ?? '';
@endphp
<aside style="width:240px;flex-shrink:0;background:#fff;border:1px solid var(--border);border-radius:12px;padding:20px 0;align-self:flex-start;">
  <div style="padding:0 20px 16px;border-bottom:1px solid var(--border);margin-bottom:8px;">
    <div style="font-size:14px;font-weight:700;color:var(--body);">{{ Auth::user()->name }}</div>
    <div style="font-size:12px;color:var(--muted);">{{ Auth::user()->email }}</div>
  </div>
  <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:{{ str_starts_with($currentRoute, 'dashboard') && !str_contains($currentRoute, '.') ? 'var(--cyan)' : 'var(--body)' }};text-decoration:none;background:{{ str_starts_with($currentRoute, 'dashboard') && !str_contains($currentRoute, '.') ? 'rgba(0,168,248,0.06)' : 'transparent' }};border-right:{{ str_starts_with($currentRoute, 'dashboard') && !str_contains($currentRoute, '.') ? '2px solid var(--cyan)' : '2px solid transparent' }};">
    <i class="ti ti-layout-dashboard" style="font-size:16px;"></i> Overview
  </a>
  <a href="{{ route('dashboard.subscriptions') }}" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:{{ str_contains($currentRoute, 'subscription') ? 'var(--cyan)' : 'var(--body)' }};text-decoration:none;background:{{ str_contains($currentRoute, 'subscription') ? 'rgba(0,168,248,0.06)' : 'transparent' }};border-right:{{ str_contains($currentRoute, 'subscription') ? '2px solid var(--cyan)' : '2px solid transparent' }};">
    <i class="ti ti-refresh" style="font-size:16px;"></i> Subscriptions
  </a>
  <a href="{{ route('dashboard.licenses') }}" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:{{ str_contains($currentRoute, 'license') ? 'var(--cyan)' : 'var(--body)' }};text-decoration:none;background:{{ str_contains($currentRoute, 'license') ? 'rgba(0,168,248,0.06)' : 'transparent' }};border-right:{{ str_contains($currentRoute, 'license') ? '2px solid var(--cyan)' : '2px solid transparent' }};">
    <i class="ti ti-key" style="font-size:16px;"></i> Licenses
  </a>
  <a href="{{ route('dashboard.orders') }}" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:{{ str_contains($currentRoute, 'order') ? 'var(--cyan)' : 'var(--body)' }};text-decoration:none;background:{{ str_contains($currentRoute, 'order') ? 'rgba(0,168,248,0.06)' : 'transparent' }};border-right:{{ str_contains($currentRoute, 'order') ? '2px solid var(--cyan)' : '2px solid transparent' }};">
    <i class="ti ti-shopping-cart" style="font-size:16px;"></i> Orders
  </a>
  <div style="border-top:1px solid var(--border);margin:8px 0;"></div>
  <a href="{{ route('dashboard.profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:{{ str_contains($currentRoute, 'profile') ? 'var(--cyan)' : 'var(--body)' }};text-decoration:none;background:{{ str_contains($currentRoute, 'profile') ? 'rgba(0,168,248,0.06)' : 'transparent' }};border-right:{{ str_contains($currentRoute, 'profile') ? '2px solid var(--cyan)' : '2px solid transparent' }};">
    <i class="ti ti-user" style="font-size:16px;"></i> Profile
  </a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0;">
    @csrf
    <button type="submit" style="display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;font-weight:600;color:var(--body);text-decoration:none;background:none;border:none;width:100%;cursor:pointer;font-family:inherit;text-align:left;">
      <i class="ti ti-logout" style="font-size:16px;"></i> Logout
    </button>
  </form>
</aside>
