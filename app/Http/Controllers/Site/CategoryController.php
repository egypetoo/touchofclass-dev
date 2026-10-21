<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->isActive()
            ->with([
                'media',
                'products' => fn ($query) => $query->active(),
                'children.products' => fn ($query) => $query->active(),
            ])
            ->orderBy('order')
            ->get();

        return view('site.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category): RedirectResponse
    {
        return redirect()->route('products.index', ['category_id' => $category->getKey()], 301);
    }
}
