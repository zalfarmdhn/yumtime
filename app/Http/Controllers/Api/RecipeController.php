<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RecipesResource;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    //
    // Controller untuk Home
    function index()
    {
        $recipes = Recipe::with(['photos', 'category'])->get();
        return RecipesResource::collection($recipes);
    }

    // Controller untuk Detail
    public function show(Recipe $recipe)
    {
        $recipe->load(['photos', 'category', 'author', 'tutorials', 'recipeIngredients.ingredient']);
        return new RecipesResource($recipe);
    }
}
