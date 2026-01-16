<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount('listings')
            ->with('breeds')
            ->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $category->load(['breeds' => function ($query) {
            $query->where('is_active', true);
        }]);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function breeds(Category $category)
    {
        $breeds = $category->breeds()
            ->where('is_active', true)
            ->withCount('listings')
            ->get();

        return response()->json([
            'breeds' => $breeds,
        ]);
    }
}
