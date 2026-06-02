<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'AutoTerra')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/plus-jakarta-sans@5/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  @php $headerCss = \App\Models\Setting::get('custom_header_css', ''); @endphp
  @if($headerCss)
  <style>{!! $headerCss !!}</style>
  @endif
  @php $headerJs = \App\Models\Setting::get('custom_header_js', ''); @endphp
  @if($headerJs)
  <script>{!! $headerJs !!}</script>
  @endif
  @yield('styles')
</head>
<body>
  @yield('body')
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    document.querySelectorAll('meta[name="csrf-token"]').forEach(m => {
      window.csrfToken = m.content;
    });
  </script>
  <script src="{{ asset('js/main.js') }}"></script>
  @yield('scripts')
  @php $footerJs = \App\Models\Setting::get('custom_footer_js', ''); @endphp
  @if($footerJs)
  <script>{!! $footerJs !!}</script>
  @endif
</body>
</html>
