<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->with('products')
            ->isActive()
            ->orderBy('order')
            ->get();

        return CategoryResource::collection($categories->load('products'));
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->load('products'));
    }
}
