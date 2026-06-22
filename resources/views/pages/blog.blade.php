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

@if(count($tags))
<div class="bl-filter">
    <span class="bl-filter-label">Filter:</span>
    <a href="/blog{{ $searchTerm ? '?q='.urlencode($searchTerm) : '' }}"
       class="bl-cat {{ !$currentTag ? 'active' : '' }}">
        All posts
    </a>
    @foreach(array_slice($tags, 0, 6) as $tag)
        <a href="/blog?tag={{ urlencode($tag) }}{{ $searchTerm ? '&q='.urlencode($searchTerm) : '' }}"
           class="bl-cat {{ $currentTag === $tag ? 'active' : '' }}">
            {{ $tag }}
        </a>
    @endforeach
</div>
@endif

<section class="blog-wrap">
  <div class="blog-layout">

    <!-- Main Content -->
    <div class="blog-main">

      <!-- Active filters -->
      @if($searchTerm || $currentTag)
      <div class="blog-filters">
        <span class="filter-label">
          @if($searchTerm)
            Results for: <strong>"{{ $searchTerm }}"</strong>
          @endif
          @if($currentTag)
            Tag: <strong>{{ $currentTag }}</strong>
          @endif
        </span>
        <a href="/blog" class="filter-clear"><i class="ti ti-x"></i> Clear</a>
      </div>
      @endif
      
      <div class="bl-articles">
        @if($posts->count())
        
         @php
        $featuredPost = $posts->first();
        @endphp

        {{-- Featured Post --}}
          <a href="/blog/{{ $featuredPost->slug }}" class="blog-featured-card">
              @if($featuredPost->featured_image)
                  <div class="blog-featured-img"
                      style="background:url({{ $featuredPost->featured_image }}) center/cover;">
                  </div>
              @else
                  <div class="blog-featured-img blog-card-img-placeholder">
                      <i class="ti ti-pencil" style="font-size:32px;color:var(--cyan);opacity:0.4;"></i>
                  </div>
              @endif

              <div class="blog-featured-content">
                  @if($featuredPost->category)
                      <span class="blog-card-cat">{{ $featuredPost->category }}</span>
                  @endif

                  <h2 class="blog-card-title">{{ $featuredPost->title }}</h2>

                  <p class="blog-card-excerpt">
                      {{ Str::limit($featuredPost->excerpt ?? strip_tags($featuredPost->content), 200) }}
                  </p>

                  <div class="blog-card-footer">
                      <span>{{ $featuredPost->author_name ?? 'AutoTerra Team' }}</span>
                      <span>
                          <i class="ti ti-eye" style="margin-right:2px;"></i>
                          {{ $featuredPost->views_count }}
                      </span>
                      <span>{{ $featuredPost->published_at?->format('M j, Y') }}</span>
                  </div>
              </div>
          </a>


        <div class="blog-grid">
          @foreach($posts as $post)
          <a href="/blog/{{ $post->slug }}" class="blog-card">
            @if($post->featured_image)
            <div class="blog-card-img" style="background:url({{ $post->featured_image }}) center/cover;"></div>
            @else
            <div class="blog-card-img blog-card-img-placeholder">
              <i class="ti ti-pencil" style="font-size:32px;color:var(--cyan);opacity:0.4;"></i>
            </div>
            @endif
            <div class="blog-card-body">
              @if($post->category)
              <span class="blog-card-cat">{{ $post->category }}</span>
              @endif
              <h3 class="blog-card-title">{{ $post->title }}</h3>
              <p class="blog-card-excerpt">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}</p>
              <div class="blog-card-footer">
                <span>{{ $post->author_name ?? 'AutoTerra Team' }}</span>
                <span><i class="ti ti-eye" style="margin-right:2px;"></i>{{ $post->views_count }}</span>
                <span>{{ $post->published_at?->format('M j, Y') ?? '' }}</span>
              </div>
            </div>
          </a>
          @endforeach
        </div>
      </div>

      <div class="blog-pagination">
        {{ $posts->links() }}
      </div>
      @else
      <div class="blog-empty">
        <i class="ti ti-pencil" style="font-size:48px;color:var(--border);margin-bottom:16px;"></i>
        <h3 style="font-size:20px;font-weight:800;color:var(--body);margin-bottom:8px;">No posts found</h3>
        <p style="font-size:14px;color:var(--muted);">
          @if($searchTerm || $currentTag)
            Try adjusting your search or filter. <a href="/blog" style="color:var(--cyan);">View all posts</a>
          @else
            Blog posts will appear here once published from the admin panel.
          @endif
        </p>
      </div>
      @endif

    </div>

    <!-- Sidebar -->
    <aside class="bl-sidebar">

      <!-- Search -->
      <div class="sidebar-card">
        <h4 class="sidebar-title"><i class="ti ti-search"></i> Search</h4>
        <form action="/blog" method="GET" class="blog-search-form">
          @if($currentTag)
          <input type="hidden" name="tag" value="{{ $currentTag }}">
          @endif
          <input type="text" name="q" value="{{ $searchTerm ?? '' }}" placeholder="Search posts..." class="blog-search-input">
          <button type="submit" class="blog-search-btn">Search</button>
        </form>
      </div>

      <!--newaletter -->
      <div class="bl-newsletter">
        {!! renderForm('blog-newsletter') !!}
      </div>

      <!-- Popular Posts -->
      @if($popularPosts->count())
      <div class="sidebar-card">
        <h4 class="sidebar-title"><i class="ti ti-flame"></i> Popular Posts</h4>
        <div class="popular-list">
          @foreach($popularPosts as $pop)
          <a href="/blog/{{ $pop->slug }}" class="popular-item">
            <div class="popular-img">
              @if($pop->featured_image)
              <img src="{{ $pop->featured_image }}" alt="{{ $pop->title }}">
              @else
              <div class="popular-img-placeholder"><i class="ti ti-pencil"></i></div>
              @endif
            </div>
            <div class="popular-info">
              <h5>{{ $pop->title }}</h5>
              <span class="popular-meta"><i class="ti ti-eye"></i> {{ number_format($pop->views_count) }} · {{ $pop->published_at?->format('M j, Y') }}</span>
            </div>
          </a>
          @endforeach
        </div>
      </div>
      @endif

      <!-- Tags -->
      @if(count($tags))
      <div class="sidebar-card">
        <h4 class="sidebar-title"><i class="ti ti-tags"></i> Tags</h4>
        <div class="tag-cloud">
          <a href="/blog" class="tag-pill {{ !$currentTag ? 'active' : '' }}">All</a>
          @foreach($tags as $tag)
          <a href="/blog?tag={{ urlencode($tag) }}{{ $searchTerm ? '&q='.urlencode($searchTerm) : '' }}" class="tag-pill {{ $currentTag === $tag ? 'active' : '' }}">{{ $tag }}</a>
          @endforeach
        </div>
      </div>
      @endif

      <!--newaletter -->
      <div class="bl-side-section">
        {!! renderForm('blog-newsletter') !!}
      </div>
    </aside>

  </div>
</section>

@include('partials.footer')
@endsection
