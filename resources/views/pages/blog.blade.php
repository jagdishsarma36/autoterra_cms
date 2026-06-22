@extends('layouts.app')
@section('title', 'Blog — AutoTerra')
@section('body')
@include('partials.nav')

<!-- hero section --> 
<section class="bl-hero">
@foreach(pageContentJson('blog', 'blog.hero') as $blogs) 
  <div class="bl-hero-inner">
    <div class="sec-eye">{!! $blogs['sec_eye'] ?? '' !!}</div>
    <h1>{!! $blogs['heading'] ?? '' !!}</h1>
    <p>{!! $blogs['description'] ?? '' !!}</p>
  </div>
@endforeach
</section>


<!-- blog filteers -->
@php
    $filters = pageContentJson('blog', 'blog.filters');
@endphp
<div class="bl-filter">
    <span class="bl-filter-label">Filter:</span>
    @foreach($filters as $filter)
        <button
            class="bl-cat {{ !empty($filter['active']) ? 'active' : '' }}"
            onclick="filterCat(this,'{{ $filter['category'] }}')">
            {{ $filter['label'] }}
        </button>
    @endforeach
</div>



<section style="padding:48px 60px;max-width:1200px;margin:0 auto;">
  @if($posts->count())
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">
    @foreach($posts as $post)
    <a href="/blog/{{ $post->slug }}" style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:box-shadow 0.2s;text-decoration:none;color:inherit;display:flex;flex-direction:column;">
      @if($post->featured_image)
      <div style="height:200px;background:url({{ $post->featured_image }}) center/cover;"></div>
      @else
      <div style="height:200px;background:linear-gradient(135deg,var(--cyan-lt),var(--off));display:flex;align-items:center;justify-content:center;">
        <i class="ti ti-pencil" style="font-size:32px;color:var(--cyan);opacity:0.4;"></i>
      </div>
      @endif
      <div style="padding:24px;flex:1;display:flex;flex-direction:column;">
        @if($post->category)
        <span style="font-size:11px;font-weight:700;color:var(--cyan);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">{{ $post->category }}</span>
        @endif
        <h3 style="font-size:17px;font-weight:800;color:var(--body);margin-bottom:8px;line-height:1.3;">{{ $post->title }}</h3>
        <p style="font-size:13px;color:var(--muted);line-height:1.65;margin-bottom:16px;flex:1;">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}</p>
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted);">
          <span>{{ $post->author_name ?? 'AutoTerra Team' }}</span>
          <span>{{ $post->published_at?->format('M j, Y') ?? '' }}</span>
        </div>
      </div>
    </a>
    @endforeach
  </div>
  <div style="margin-top:40px;text-align:center;">
    {{ $posts->links() }}
  </div>
  @else
  <div style="text-align:center;padding:80px 0;">
    <i class="ti ti-pencil" style="font-size:48px;color:var(--border);margin-bottom:16px;"></i>
    <h3 style="font-size:20px;font-weight:800;color:var(--body);margin-bottom:8px;">No posts yet</h3>
    <p style="font-size:14px;color:var(--muted);">Blog posts will appear here once published from the admin panel.</p>
  </div>
  @endif
</section>

@include('partials.footer')
@endsection
