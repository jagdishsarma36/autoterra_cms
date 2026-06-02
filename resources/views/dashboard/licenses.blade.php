@extends('layouts.app')
@section('title', 'Licenses — AutoTerra')
@section('body')
@include('partials.nav')
<div style="display:flex;gap:32px;padding:48px 60px;max-width:1200px;margin:0 auto;align-items:flex-start;">
@include('partials.dashboard-sidebar')
<div style="flex:1;min-width:0;">
  <h1 style="font-size:28px;font-weight:800;margin-bottom:24px;">My License Keys</h1>
  <table style="width:100%;border-collapse:collapse;">
    <tr style="background:var(--navy);color:#fff;">
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Product</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">License Key</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Status</th>
      <th style="padding:10px 14px;text-align:left;font-size:12px;">Expires</th>
    </tr>
    @forelse($licenses as $license)
    <tr style="border-bottom:1px solid var(--border);">
      <td style="padding:12px 14px;font-size:13px;font-weight:700;">{{ $license->product->name ?? 'N/A' }}</td>
      <td style="padding:12px 14px;font-size:12px;font-family:monospace;background:var(--off);border-radius:4px;">{{ $license->license_key }}</td>
      <td style="padding:12px 14px;"><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $license->is_active && !$license->isExpired() ? '#D1FAE2' : '#FEE2E2' }};color:{{ $license->is_active && !$license->isExpired() ? '#065F46' : '#991B1B' }};">{{ $license->is_active && !$license->isExpired() ? 'Active' : 'Expired' }}</span></td>
      <td style="padding:12px 14px;font-size:13px;">{{ $license->expires_at?->format('M j, Y') ?? 'N/A' }}</td>
    </tr>
    @empty
    <tr><td colspan="4" style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">No license keys yet. <a href="/buy" style="color:var(--cyan);">Purchase a product →</a></td></tr>
    @endforelse
  </table>
</div>
</div>

@include('partials.footer')
@endsection
