<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::isActive()
            ->orderBy('order', 'asc')
            ->get();

        return BlogResource::collection($blogs);
    }

    public function show(Blog $blog)
    {
        $blog['relatedBlogs'] = Blog::isActive()
            ->where('id', '!=', $blog->id)
            ->orderBy('order', 'asc')
            ->take(3)
            ->get();

        return new BlogResource($blog);
    }
}
