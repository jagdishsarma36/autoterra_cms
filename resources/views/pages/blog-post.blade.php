@extends('layouts.app')
@section('title', $post->meta_title ?: $post->title . ' — AutoTerra Blog')
@section('meta_description', $post->meta_description ?: 'Read the latest news and updates from the AutoTerra Blog.')
@section('body')
@include('partials.nav')

<article style="max-width:800px;margin:0 auto;padding:48px 60px;">
  <a href="/blog" style="font-size:13px;color:var(--cyan);display:inline-flex;align-items:center;gap:4px;margin-bottom:24px;text-decoration:none;"><i class="ti ti-arrow-left"></i> Back to blog</a>

  @if($post->category)
  <span style="font-size:11px;font-weight:700;color:var(--cyan);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;display:inline-block;">{{ $post->category }}</span>
  @endif

  <h1 style="font-size:32px;font-weight:800;color:var(--body);line-height:1.2;margin-bottom:16px;">{{ $post->title }}</h1>

  <div style="display:flex;gap:16px;align-items:center;margin-bottom:32px;padding-bottom:24px;border-bottom:1px solid var(--border);">
    <span style="font-size:13px;color:var(--muted);">{{ $post->author_name ?? 'AutoTerra Team' }}</span>
    <span style="font-size:13px;color:var(--border);">·</span>
    <span style="font-size:13px;color:var(--muted);">{{ $post->published_at?->format('F j, Y') }}</span>
    @if($post->tags && count($post->tags))
    <span style="font-size:13px;color:var(--border);">·</span>
    <div style="display:flex;gap:6px;">
      @foreach($post->tags as $tag)
      <span style="font-size:11px;background:var(--off);border:1px solid var(--border);border-radius:20px;padding:3px 10px;color:var(--muted);font-weight:600;">{{ $tag }}</span>
      @endforeach
    </div>
    @endif
  </div>

  @if($post->featured_image)
  <div style="margin-bottom:32px;border-radius:12px;overflow:hidden;">
    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width:100%;height:auto;display:block;">
  </div>
  @endif

  <div class="blog-content" style="font-size:16px;color:var(--body);line-height:1.8;">
    {!! $post->content !!}
  </div>
</article>

@include('partials.footer')
@endsection

@section('styles')
<style>
  .blog-content h2 { font-size: 24px; font-weight: 800; color: var(--body); margin: 32px 0 16px; }
  .blog-content h3 { font-size: 20px; font-weight: 700; color: var(--body); margin: 28px 0 12px; }
  .blog-content p { margin-bottom: 16px; }
  .blog-content ul, .blog-content ol { margin-bottom: 16px; padding-left: 24px; }
  .blog-content li { margin-bottom: 6px; }
  .blog-content blockquote { border-left: 3px solid var(--cyan); padding: 12px 20px; background: var(--off); border-radius: 0 8px 8px 0; margin: 20px 0; font-style: italic; color: var(--muted); }
  .blog-content code { background: var(--off); padding: 2px 6px; border-radius: 4px; font-size: 14px; }
  .blog-content pre { background: var(--navy); color: #e2e8f0; padding: 20px; border-radius: 8px; overflow-x: auto; margin: 20px 0; }
  .blog-content pre code { background: none; padding: 0; color: inherit; }
  .blog-content img { max-width: 100%; border-radius: 8px; margin: 16px 0; }
  .blog-content a { color: var(--cyan); text-decoration: underline; }
</style>
@endsection
