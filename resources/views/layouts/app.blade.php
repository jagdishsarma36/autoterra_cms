<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'AutoTerra')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Preload CSS -->
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" as="style">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" as="style">

  <!-- Load CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/plus-jakarta-sans@5/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
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
  <!-- jQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Owl Carousel -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  @yield('body')
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    document.querySelectorAll('meta[name="csrf-token"]').forEach(m => {
      window.csrfToken = m.content;
    });
  </script>
  <script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
  @yield('scripts')
  @php $footerJs = \App\Models\Setting::get('custom_footer_js', ''); @endphp
  @if($footerJs)
  <script>{!! $footerJs !!}</script>
  @endif
</body>
</html>
