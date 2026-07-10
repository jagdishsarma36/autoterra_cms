@extends('layouts.app')
@section('title', 'Contact — AutoTerra')
@section('meta_description', 'Contact AutoTerra for sales, product support, or general inquiries. Our team is here to help with geospatial software and LiDAR solutions.')
@section('body')
@include('partials.nav')

@php
  $form = App\Models\FormCms::where('slug', 'tell-us-what-you-need')->where('is_active', true)->first();
  $fields = $form ? $form->fields()->orderBy('sort_order')->get() : collect();
@endphp

<!-- Hero Section -->
<section class="ct-hero" style="background:var(--navy);padding:56px 60px;">
  <div class="ct-hero-inner">
    <h1>{!! pageContent('contact', 'hero.heading') !!}</h1>
    <p>{{ pageContent('contact', 'hero.description') }}</p>
  </div>
</section>
<!-- Hero Section End -->

<!-- contact form and info -->
<section >
<div class="ct-main">
  <!-- ── LEFT: FORM ── -->
  <div class="ct-form-wrap">
    <div id="ctFormContent">
      <p class="ct-form-title">{{ $form->name }}</p>
      @if($form->description)
        <p class="ct-form-sub"> {{ $form->description }} </p>
      @endif
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
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="form_{{ $form->slug }}">
        @foreach($fields as $field)
        @php $span = $field->width == 100 || $field->width == 1 ? 'grid-column: 1 / -1;' : ''; @endphp
        <div style="{{ $span }}" class="field_width_{{ $field->width }} field_wrap_{{ $field->name }}" data-name="{{ $field->name }}" data-type="{{ $field->type }}">
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
              <label class="radio-label field_lbl_{{ str_replace(' ', '_', $option) }}" style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
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
              <label class="checkbox-label field_lbl_{{ str_replace(' ', '_', $option) }}" style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--body);cursor:pointer;">
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
    </div>
  </div>
  <div class="ct-info-wrap">
    <!-- What happens next -->
    <div class="ct-steps">
      <div class="ct-steps-title">{{ pageContent('contact', 'form.heading') }}</div>
      @foreach(pageContentJson('contact', 'contact.info') as $contactInfo)
      <div class="ct-step">
         <div class="ct-step-num">{{ $loop->iteration }}</div>
        <div class="ct-step-body">
          <h4>{{ $contactInfo['info_title'] }}</h4>
          <p>{{ $contactInfo['info_text'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
    <div class="ct-divider"></div>
    <!-- Contact details -->
    <div class="ct-contact-items">
    @foreach(pageContentJson('contact', 'form.contact') as $contactform)
      <div class="ct-contact-item">
        <div class="ct-contact-icon"><i class="ti {{ $contactform['icon_class'] }}"></i></div>
        <div>
          <div class="ct-contact-lbl">{{ $contactform['name'] }}</div>
          <div class="ct-contact-val">
            @php
                $url = $contactform['link_url'];
                if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
                    $href = 'mailto:' . $url;
                } elseif (preg_match('/^[0-9+\-\s()]+$/', $url)) {
                    $href = 'tel:' . preg_replace('/\s+/', '', $url);
                } else {
                    $href = $url;
                }
            @endphp
            <a href="{{ $href }}">
              {{ $contactform['link_text'] }}
            </a>
          </div>
        </div>
      </div>
    @endforeach
    </div>
    <!-- Response time indicator -->
    <div class="ct-resp-badge">
      <i class="ti ti-clock-check"></i>
      <span><strong>Typical response time:</strong> within 1 business day for demos and trials. Support tickets: 4 business hours for Pro Spatial customers.</span>
    </div>
    <div class="ct-divider"></div>
    <!-- Offices -->
    <div class="ct-offices">
      <div class="ct-offices-title">{{ pageContent('contact', 'contact.address_heading') }}</div>
      @foreach(pageContentJson('contact', 'contact.address') as $contactaddress)
      <div class="ct-office">
        <i class="ti {{ $contactaddress['icon_class'] }}"></i>
        <div>
          <strong>{{ $contactaddress['add_title'] }}</strong><br>
          {{ $contactaddress['text'] }}
        </div>
      </div>
      @endforeach
    </div>
  </div><!-- /ct-info-wrap -->
</div>
</section>
@include('partials.footer')
@endsection
