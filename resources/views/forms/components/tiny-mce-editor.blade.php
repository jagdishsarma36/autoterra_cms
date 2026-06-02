@php
    $statePath = $getStatePath();
    $id = str_replace('.', '-', $statePath);
@endphp

<div wire:ignore x-data="{ value: @entangle($statePath) }" x-init="$watch('value', val => { if (window.tinymce && tinymce.get('tiny-{{ $id }}')) { if (tinymce.get('tiny-{{ $id }}').getContent() !== val) { tinymce.get('tiny-{{ $id }}').setContent(val || ''); } } })">
    <textarea id="tiny-{{ $id }}"
        x-ref="editor"
        x-on:input="value = $el.value"
        style="visibility:hidden;">{{ $getState() }}</textarea>
</div>

@script
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editorId = 'tiny-{{ $id }}';
    if (typeof tinymce !== 'undefined') {
        initTinyMCE(editorId);
    } else {
        const checkInterval = setInterval(function() {
            if (typeof tinymce !== 'undefined') {
                clearInterval(checkInterval);
                initTinyMCE(editorId);
            }
        }, 100);
    }
});

function initTinyMCE(editorId) {
    tinymce.init({
        selector: '#' + editorId,
        height: {{ $getHeight() }},
        menubar: 'edit view insert format tools table',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount',
            'emoticons', 'codesample'
        ],
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image table | ' +
            'removeformat code fullscreen | help',
        content_style: 'body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; font-size: 14px; line-height: 1.7; color: #1C2B3A; }',
        skin: false,
        branding: false,
        promotion: false,
        convert_urls: false,
        relative_urls: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
                const textarea = editor.getElement();
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            });
            editor.on('init', function() {
                const textarea = editor.getElement();
                if (textarea.value) {
                    editor.setContent(textarea.value);
                }
            });
        }
    });
}
</script>
@endscript
