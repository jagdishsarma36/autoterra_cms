@php
    $statePath = $getStatePath();
    $id = str_replace('.', '-', $statePath);
    $value = $getState() ?? '';
@endphp

<div wire:ignore x-data="{ value: @entangle($statePath) }">
    <style>
        .html-editor-{{ $id }} {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }
        .html-editor-{{ $id }} .toolbar {
            background: #F5F8FC;
            border-bottom: 1px solid var(--border);
            padding: 6px 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
        }
        .html-editor-{{ $id }} .toolbar button {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 5px 8px;
            cursor: pointer;
            font-size: 13px;
            color: #5A7A96;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 3px;
            transition: all 0.1s;
        }
        .html-editor-{{ $id }} .toolbar button:hover {
            background: #E0F4FF;
            border-color: #B3E0FF;
            color: #0860B0;
        }
        .html-editor-{{ $id }} .toolbar .sep {
            width: 1px;
            background: var(--border);
            margin: 2px 4px;
        }
        .html-editor-{{ $id }} textarea {
            width: 100%;
            min-height: 250px;
            padding: 16px;
            border: none;
            font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.6;
            color: #1C2B3A;
            resize: vertical;
            outline: none;
            background: #fff;
            box-sizing: border-box;
        }
        .html-editor-{{ $id }} textarea:focus {
            box-shadow: inset 0 0 0 2px rgba(0,168,248,0.2);
        }
    </style>

    <div class="html-editor-{{ $id }}">
        <div class="toolbar">
            <button type="button" onclick="insertTag('{{ $id }}', 'h1')" title="Heading 1"><b>H1</b></button>
            <button type="button" onclick="insertTag('{{ $id }}', 'h2')" title="Heading 2"><b>H2</b></button>
            <button type="button" onclick="insertTag('{{ $id }}', 'h3')" title="Heading 3"><b>H3</b></button>
            <button type="button" onclick="insertTag('{{ $id }}', 'h4')" title="Heading 4"><b>H4</b></button>
            <div class="sep"></div>
            <button type="button" onclick="insertTag('{{ $id }}', 'p')" title="Paragraph">¶</button>
            <button type="button" onclick="insertTag('{{ $id }}', 'strong')" title="Bold"><b>B</b></button>
            <button type="button" onclick="insertTag('{{ $id }}', 'em')" title="Italic"><i>I</i></button>
            <button type="button" onclick="insertTag('{{ $id }}', 'u')" title="Underline"><u>U</u></button>
            <button type="button" onclick="insertTag('{{ $id }}', 's')" title="Strikethrough"><s>S</s></button>
            <div class="sep"></div>
            <button type="button" onclick="insertTag('{{ $id }}', 'ul')" title="Bullet List">• List</button>
            <button type="button" onclick="insertTag('{{ $id }}', 'ol')" title="Numbered List">1. List</button>
            <button type="button" onclick="insertTag('{{ $id }}', 'li')" title="List Item">- Item</button>
            <div class="sep"></div>
            <button type="button" onclick="insertLink('{{ $id }}')" title="Link">🔗 Link</button>
            <button type="button" onclick="insertTag('{{ $id }}', 'blockquote')" title="Blockquote">❝ Quote</button>
            <div class="sep"></div>
            <button type="button" onclick="insertTag('{{ $id }}', 'hr')" title="Horizontal Rule">― HR</button>
            <button type="button" onclick="insertTag('{{ $id }}', 'br')" title="Line Break">↵ BR</button>
            <button type="button" onclick="wrapTag('{{ $id }}', 'div', 'style=\"background:#F5F8FC;border-radius:8px;padding:20px;margin:16px 0;\"')" title="Card Div">□ Card</button>
        </div>
        <textarea id="html-editor-{{ $id }}"
            x-ref="editor"
            x-on:input="value = $el.value">{{ $value }}</textarea>
    </div>
</div>

@script
<script>
function insertTag(id, tag) {
    const ta = document.getElementById('html-editor-' + id);
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value;
    const selected = text.substring(start, end);

    const selfClosing = ['hr', 'br'];
    let insert = '';
    if (selfClosing.includes(tag)) {
        insert = '<' + tag + '>';
    } else if (selected) {
        insert = '<' + tag + '>' + selected + '</' + tag + '>';
    } else {
        insert = '<' + tag + '></' + tag + '>';
    }

    ta.value = text.substring(0, start) + insert + text.substring(end);
    ta.selectionStart = ta.selectionEnd = start + insert.length;
    ta.focus();
    ta.dispatchEvent(new Event('input', { bubbles: true }));
}

function insertLink(id) {
    const ta = document.getElementById('html-editor-' + id);
    if (!ta) return;
    const url = prompt('Enter URL:', 'https://');
    if (!url) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value;
    const selected = text.substring(start, end) || 'link text';
    const insert = '<a href="' + url + '">' + selected + '</a>';
    ta.value = text.substring(0, start) + insert + text.substring(end);
    ta.selectionStart = ta.selectionEnd = start + insert.length;
    ta.focus();
    ta.dispatchEvent(new Event('input', { bubbles: true }));
}

function wrapTag(id, tag, attrs) {
    const ta = document.getElementById('html-editor-' + id);
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value;
    const selected = text.substring(start, end) || 'content';
    const insert = '<' + tag + ' ' + attrs + '>' + selected + '</' + tag + '>';
    ta.value = text.substring(0, start) + insert + text.substring(end);
    ta.selectionStart = ta.selectionEnd = start + insert.length;
    ta.focus();
    ta.dispatchEvent(new Event('input', { bubbles: true }));
}
</script>
@endscript
