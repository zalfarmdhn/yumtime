<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::withCount('recipes')->get();
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        $category->load(['recipes.category', 'recipes.author']);
        $category->loadCount('recipes');
        return new CategoryResource($category);
    }

    public function showLatest(Category $category)
    {
        $category->load(['recipes' => function ($query) {
            $query->latest();
        }, 'recipes.category', 'recipes.author']);
        $category->loadCount('recipes');
        $category->recipes = $category->recipes->take(3);
        return new CategoryResource($category);
    }
}
