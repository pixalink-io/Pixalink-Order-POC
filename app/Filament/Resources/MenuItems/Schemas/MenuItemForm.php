<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state)))
                    ->maxLength(255),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),

                FileUpload::make('image')
                    ->disk('public')
                    ->directory('menu-items')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_available')
                    ->default(true),

                Toggle::make('is_featured')
                    ->default(false),
            ]);
    }
}
