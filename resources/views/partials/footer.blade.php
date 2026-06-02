@php
  $logoA = \App\Models\Setting::get('footer_logo_a', 'AUTO');
  $logoT = \App\Models\Setting::get('footer_logo_t', 'TERRA');
  $logoImage = \App\Models\Setting::get('footer_logo_image', '');
  $footerDesc = \App\Models\Setting::get('footer_description', 'Full lifecycle geospatial software for survey, design, construction, maintenance and monitoring of infrastructure.');
  $footerLinks = json_decode(\App\Models\Setting::get('footer_links', '[{"label":"Products","url":"/products"},{"label":"Pricing","url":"/pricing"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"},{"label":"Privacy Policy","url":"/privacy-policy"},{"label":"Terms of Service","url":"/terms-of-service"}]'), true);
  $copyright = \App\Models\Setting::get('footer_copyright', '© ' . date('Y') . ' AutoTerra. All rights reserved.');
@endphp
<footer>
  <div class="foot-grid">
    <div>
      <div class="foot-logo">
        @if($logoImage)
          <img src="{{ $logoImage }}" alt="{{ $logoA }}{{ $logoT }}" style="height:28px;width:auto;">
        @else
          <span class="logo-a">{{ $logoA }}</span><span class="logo-t">{{ $logoT }}</span>
        @endif
      </div>
      <p class="foot-desc">{{ $footerDesc }}</p>
      <div class="foot-social">
        <a href="https://www.facebook.com/infycons/" title="Facebook"><i class="ti ti-brand-facebook"></i></a>
        <a href="https://in.linkedin.com/company/infycons-creative-software-pvt-ltd-" title="LinkedIn"><i class="ti ti-brand-linkedin"></i></a>
        <a href="https://twitter.com/infycons" title="X / Twitter"><i class="ti ti-brand-x"></i></a>
        <a href="#" title="Instagram"><i class="ti ti-brand-instagram"></i></a>
        <a href="#" title="YouTube"><i class="ti ti-brand-youtube"></i></a>
      </div>
    </div>
    <div>
        <div class="foot-col-title">Products</div>
        <div class="foot-links">
          @foreach($footerLinks as $link)
          <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
          @endforeach
        </div>
    </div>
    <div>
        <div class="foot-col-title">Resources</div>
        <div class="foot-links">
          @foreach(pageContentJson('global', 'footer.resources') as $resources)
          <a href="{{ $resources['url_link'] }}">{{ $resources['url_text'] }}</a>
          @endforeach
        </div>
    </div>
    <div>
        <div class="foot-col-title">Company</div>
        <div class="foot-links">
          @foreach(pageContentJson('global', 'footer.company') as $company)
          <a href="{{ $company['url_link'] }}">{{ $company['url_text'] }}</a>
          @endforeach
        </div>
    </div>
  </div>
  <div class="foot-bottom">
    <span class="foot-copy">{{ $copyright }}</span>
    <div class="foot-legal">
      @foreach(pageContentJson('global', 'footer.legal') as $legal)
          <a href="{{ $legal['url_link'] }}">{{ $legal['url_text'] }}</a>
      @endforeach    
    </div>
  </div>
</footer>
