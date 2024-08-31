<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Filament\Resources\RecipeResource\RelationManagers\TutorialsRelationManager;
use App\Models\Ingredient;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('thumbnail')
                ->image()
                ->required(),

                Forms\Components\Textarea::make('about')
                ->required()
                ->rows(10)
                ->cols(30),

                Forms\Components\Repeater::make('recipeIngredients')
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('ingredient_id')
                    ->relationship('ingredient', 'name')
                    ->required(),
                ]),

                Forms\Components\Repeater::make('photos')
                ->relationship('photos')
                ->schema([
                    Forms\Components\FileUpload::make('photo')
                    ->required(),
                ]),

                Forms\Components\Select::make('recipe_author_id')
                ->relationship('author', 'name')
                ->searchable()
                ->preload()
                ->required(),

                Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),

                Forms\Components\TextInput::make('url_video')
                ->required()
                ->maxLength(255),

                Forms\Components\FileUpload::make('url_file')
                ->downloadable()
                ->uploadingMessage('Uploading Recipes...')
                ->acceptedFileTypes((['application/pdf']))
                ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),

                ImageColumn::make('author.photo')
                    ->circular(),

                ImageColumn::make('thumbnail')
            ])
            ->filters([
                //
                SelectFilter::make('recipe_author_id')
                ->label('Author')
                ->relationship('author', 'name'),

                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name'),

                SelectFilter::make('ingredient_id')
                ->label('Ingredient')
                ->options(Ingredient::pluck('name', 'id'))
                ->query(function(Builder $query, array $data) {
                    if($data['value']) {
                        $query->whereHas('recipeIngredients', function ($query) use ($data) {
                            $query->where(('ingredient_id'), $data['value']);
                        });
                    }
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            // Penerapan one-to-many (HasMany)
            TutorialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
