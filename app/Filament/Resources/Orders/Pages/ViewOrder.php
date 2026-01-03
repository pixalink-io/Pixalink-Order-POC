<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;


class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Orders')
                ->url(OrderResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            Action::make('confirm')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->visible(fn(): bool => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->confirm();
                    $this->refreshFormData(['status', 'confirmed_at']);
                }),

            Action::make('preparing')
                ->icon('heroicon-o-fire')
                ->color('warning')
                ->visible(fn(): bool => $this->record->status === 'confirmed')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->startPreparing();
                    $this->refreshFormData(['status', 'prepared_at']);
                }),

            Action::make('ready')
                ->icon('heroicon-o-bell-alert')
                ->color('success')
                ->visible(fn(): bool => $this->record->status === 'preparing')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->markReady();
                    $this->refreshFormData(['status', 'ready_at']);
                }),

            Action::make('complete')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn(): bool => $this->record->status === 'ready')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->complete();
                    $this->refreshFormData(['status', 'completed_at']);
                }),
        ];
    }
}
