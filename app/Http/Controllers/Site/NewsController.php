<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('site.news.index', [
            'blogs' => Blog::query()
                ->with('media')
                ->isActive()
                ->orderByDesc('published_at')
                ->paginate(7)
                ->withQueryString(),
        ]);
    }

    public function show(Blog $blog): View|RedirectResponse
    {
        if ($redirect = $blog->redirectIfSlugLocaleMismatch()) {
            return $redirect;
        }

        return view('site.news.show', [
            'blog' => $blog,
            'latestPosts' => Blog::query()
                ->with('media')
                ->isActive()
                ->whereKeyNot($blog->id)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
