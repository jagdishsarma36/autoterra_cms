<div style="padding: 16px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-color-muted); margin-bottom: 5px;">Media URL</label>
    <div style="display: flex; gap: 8px; align-items: center;">
        <input type="text" id="media-url-input" value="{{ $url }}" readonly
            style="flex: 1; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace; font-size: 13px; background: var(--bg-gray-50); color: var(--text-color);">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('media-url-input').value).then(function(){ document.getElementById('copy-feedback').style.display='inline'; setTimeout(function(){ document.getElementById('copy-feedback').style.display='none'; }, 2000); })"
            style="padding: 8px 16px; background: var(--primary-500); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: white; cursor: pointer; white-space: nowrap;">
            Copy
        </button>
    </div>
    <span id="copy-feedback" style="display: none; font-size: 12px; color: var(--success-500); margin-top: 4px;">Copied!</span>
</div>
