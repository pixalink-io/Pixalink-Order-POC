<?php

namespace App\Filament\Resources\MenuItems\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OptionGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'optionGroups';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Group Name'),
                    
                Select::make('type')
                    ->options([
                        'single' => 'Single Selection (Radio)',
                        'multiple' => 'Multiple Selection (Checkbox)',
                    ])
                    ->required()
                    ->default('single')
                    ->label('Selection Type'),
                    
                Toggle::make('is_required')
                    ->label('Required')
                    ->default(false),
                    
                TextInput::make('min_selections')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Minimum Selections'),
                    
                TextInput::make('max_selections')
                    ->numeric()
                    ->minValue(1)
                    ->label('Maximum Selections')
                    ->helperText('Leave empty for unlimited'),
                    
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),
                
                Repeater::make('options')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Option Name'),
                            
                        TextInput::make('price_adjustment')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0)
                            ->label('Price Adjustment'),
                            
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label('Sort Order'),
                            
                        Toggle::make('is_available')
                            ->default(true)
                            ->label('Available'),
                    ])
                    ->columns(4)
                    ->reorderable('sort_order')
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->columnSpanFull()
                    ->label('Options'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Group Name'),
                    
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'info',
                        'multiple' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'single' => 'Single',
                        'multiple' => 'Multiple',
                        default => $state,
                    }),
                    
                IconColumn::make('is_required')
                    ->boolean()
                    ->label('Required'),
                    
                TextColumn::make('options_count')
                    ->counts('options')
                    ->label('Options')
                    ->badge(),
                    
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable()
                    ->label('Order'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }
}
