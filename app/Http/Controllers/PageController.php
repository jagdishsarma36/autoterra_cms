<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageCms;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function products()
    {
        return view('pages.products');
    }

    public function pricing()
    {
        return view('pages.pricing');
    }

    public function buy()
    {
        return view('pages.buy');
    }

    public function pro()
    {
        return view('pages.pro');
    }

    public function proSpatial()
    {
        return view('pages.pro-spatial');
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function resources()
    {
        return view('pages.resources');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function quote()
    {
        return view('pages.quote');
    }

    public function blog(Request $request)
    {
        $query = Post::published();

        $searchTerm = $request->input('q');
        $currentTag = $request->input('tag');

        if ($searchTerm) {
            $query->search($searchTerm);
        }

        if ($currentTag) {
            $query->withTag($currentTag);
        }

        $posts = $query->latest('published_at')->paginate(12)->withQueryString();

        $tags = Post::published()
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->sort()
            ->toArray();

        $popularPosts = Post::popular(5)->get();

        return view('pages.blog', compact('posts', 'tags', 'currentTag', 'searchTerm', 'popularPosts'));
    }

    public function blogPost(Post $post)
    {
        if (!$post->is_published || ($post->published_at && $post->published_at->isFuture())) {
            abort(404);
        }
        $post->increment('views_count');
        return view('pages.blog-post', compact('post'));
    }

    public function dynamicPage(PageCms $page)
    {
        if (!$page->is_published || ($page->published_at && $page->published_at->isFuture())) {
            abort(404);
        }
        return view('pages.dynamic-page', compact('page'));
    }

    /**
     * CMS page at root URL: /{slug}
     * Supports nested slugs like /test/my-page.
     * Falls through to 404 if no CMS page matches.
     */
    public function cmsPage(string $slug)
    {
        $page = PageCms::where('slug', $slug)->first();
        if (!$page) {
            abort(404);
        }
        if (!$page->is_published || ($page->published_at && $page->published_at->isFuture())) {
            abort(404);
        }
        return view('pages.dynamic-page', compact('page'));
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function eula()
    {
        return view('pages.eula');
    }

    public function cookies()
    {
        return view('pages.cookies');
    }

    public function adminInvoicePrint(Order $order)
    {
        if (!Auth::check()) {
            abort(403);
        }
        $order->load(['product', 'licenseKeys', 'user']);
        return view('admin.invoice-print', compact('order'));
    }

    public function getSetting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}
