<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url_video' => $this->url_video,
            'url_file' => $this->url_file,
            'thumbnail' => $this->thumbnail,
            'about' => $this->about,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'recipe_ingredients' => RecipeIngredientResource::collection($this->whenLoaded('recipeIngredients')),
            'photos' => RecipePhotoResource::collection($this->whenLoaded('photos')),
            'tutorials' => RecipeTutorialResource::collection($this->whenLoaded('tutorials')),
            'author' => new RecipeAuthorsResource($this->whenLoaded('author')),
        ];
    }
}
