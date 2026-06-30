<div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 24px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1e293b,#0f172a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
            <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ auth()->user()->name }}</div>
            <div style="font-size:12px;color:#94a3b8;">{{ auth()->user()->email }}</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ filament()->getLogoutUrl() }}" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:600;color:#ef4444;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sign out
        </a>
    </div>
</div>
