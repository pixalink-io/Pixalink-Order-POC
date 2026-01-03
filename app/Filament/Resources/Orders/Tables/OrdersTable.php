<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Order #'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'confirmed' => 'info',
                        'preparing' => 'warning',
                        'ready' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->label('Total'),

                Tables\Columns\TextColumn::make('orderItems_count')
                    ->counts('orderItems')
                    ->label('Items')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->label('Ordered'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'preparing' => 'Preparing',
                        'ready' => 'Ready',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn(Order $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn(Order $record) => $record->confirm()),

                Action::make('preparing')
                    ->icon('heroicon-o-fire')
                    ->color('warning')
                    ->visible(fn(Order $record): bool => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(fn(Order $record) => $record->startPreparing()),

                Action::make('ready')
                    ->icon('heroicon-o-bell-alert')
                    ->color('success')
                    ->visible(fn(Order $record): bool => $record->status === 'preparing')
                    ->requiresConfirmation()
                    ->action(fn(Order $record) => $record->markReady()),

                Action::make('complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Order $record): bool => $record->status === 'ready')
                    ->requiresConfirmation()
                    ->action(fn(Order $record) => $record->complete()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
