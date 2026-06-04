@extends('layouts.app')
@section('title', $form->name . ' — AutoTerra')
@section('body')
@include('partials.nav')

<section style="background:var(--navy);padding:60px;">
  <h1 style="font-size:38px;font-weight:800;color:#fff;margin-bottom:14px;">{{ $form->name }}</h1>
  @if($form->description)
  <p style="font-size:15px;color:rgba(210,230,248,0.5);max-width:600px;">{{ $form->description }}</p>
  @endif
</section>

<section style="max-width:700px;margin:0 auto;padding:48px 60px;">
  @if(session('form_success'))
  <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
    <i class="ti ti-check-circle" style="font-size:20px;color:#065F46;"></i>
    <span style="font-size:14px;color:#065F46;font-weight:600;">{{ session('form_success') }}</span>
  </div>
  @endif

  @if($errors->any())
  <div style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
    <div style="font-size:14px;color:#B91C1C;font-weight:600;margin-bottom:8px;">Please fix the following errors:</div>
    @foreach($errors->all() as $error)
    <div style="font-size:13px;color:#B91C1C;">• {{ $error }}</div>
    @endforeach
  </div>
  @endif

  <form method="POST" action="{{ route('form.submit', $form->slug) }}">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      @foreach($fields as $field)
      @php $span = $field->width == 100 ? 'grid-column: 1 / -1;' : ''; @endphp
      <div style="{{ $span }}" class="field_width_{{ $field->width }}">
        @if($field->type === 'hidden')
          <input type="hidden" name="field_{{ $field->name }}" value="{{ $field->placeholder }}">
        @else
          <label style="display:block;font-size:13px;font-weight:600;color:var(--body);margin-bottom:5px;">
            {{ $field->label }} @if($field->is_required)<span style="color:#EF4444;">*</span>@endif
          </label>
        @endif

        @if($field->type === 'text' || $field->type === 'email' || $field->type === 'number' || $field->type === 'phone' || $field->type === 'date' || $field->type === 'time')
          <input type="{{ $field->type === 'phone' ? 'tel' : $field->type }}"
            name="field_{{ $field->name }}"
            placeholder="{{ $field->placeholder }}"
            value="{{ old('field_' . $field->name) }}"
            @if($field->is_required) required @endif
            style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;outline:none;">
        @endif

        @if($field->type === 'textarea')
          <textarea name="field_{{ $field->name }}"
            placeholder="{{ $field->placeholder }}"
            @if($field->is_required) required @endif
            rows="4"
            style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);box-sizing:border-box;resize:vertical;outline:none;">{{ old('field_' . $field->name) }}</textarea>
        @endif

        @if($field->type === 'select')
          <select name="field_{{ $field->name }}"
            @if($field->is_required) required @endif
            style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:7px;font-family:inherit;font-size:14px;color:var(--body);background:#fff;box-sizing:border-box;">
            <option value="">{{ $field->placeholder ?: 'Select...' }}</option>
            @foreach($field->options as $option)
            <option value="{{ $option }}" {{ old('field_' . $field->name) === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
          </select>
        @endif

        @if($field->type === 'radio')
          <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
            @foreach($field->options as $option)
            <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
              <input type="radio" name="field_{{ $field->name }}" value="{{ $option }}"
                {{ old('field_' . $field->name) === $option ? 'checked' : '' }}
                @if($field->is_required) required @endif
                style="accent-color:var(--cyan);">
              {{ $option }}
            </label>
            @endforeach
          </div>
        @endif

        @if($field->type === 'checkbox')
          <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
            @foreach($field->options as $option)
            <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
              <input type="checkbox" name="field_{{ $field->name }}[]" value="{{ $option }}"
                {{ in_array($option, old('field_' . $field->name, [])) ? 'checked' : '' }}
                style="accent-color:var(--cyan);">
              {{ $option }}
            </label>
            @endforeach
          </div>
        @endif

        @if($field->type === 'file')
          <input type="file" name="field_{{ $field->name }}"
            @if($field->is_required) required @endif
            style="width:100%;padding:8px;border:1px solid var(--border);border-radius:7px;font-size:13px;">
        @endif

        @if($field->help_text)
        <p style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $field->help_text }}</p>
        @endif
      </div>
      @endforeach
    </div>

    <div style="margin-top:24px;">
      <button type="submit"
        style="width:100%;padding:13px 0;background:var(--cyan);color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:background 0.2s;">
        {{ $form->submit_button_text }}
      </button>
    </div>
  </form>
</section>

@include('partials.footer')
@endsection
