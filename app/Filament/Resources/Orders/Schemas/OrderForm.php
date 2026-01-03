<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        TextInput::make('order_number')
                            ->disabled()
                            ->dehydrated()
                            ->label('Order Number'),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false)
                            ->label('Status')
                            ->helperText('✅ Change the order status here'),
                    ])->columns(2),

                Section::make('Customer Information')
                    ->description('🔒 Customer information is read-only')
                    ->schema([
                        TextInput::make('customer_name')
                            ->disabled()
                            ->dehydrated()
                            ->label('Customer Name'),

                        TextInput::make('customer_phone')
                            ->disabled()
                            ->dehydrated()
                            ->label('Phone Number'),

                        TextInput::make('customer_email')
                            ->disabled()
                            ->dehydrated()
                            ->label('Email Address'),

                        Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->label('Notes')
                            ->helperText('✅ You can edit notes'),
                    ])->columns(2),

                Section::make('Order Totals')
                    ->description('🔒 Totals are calculated automatically')
                    ->schema([
                        TextInput::make('subtotal')
                            ->disabled()
                            ->dehydrated()
                            ->prefix('$')
                            ->label('Subtotal'),

                        TextInput::make('tax')
                            ->disabled()
                            ->dehydrated()
                            ->prefix('$')
                            ->label('Tax Amount'),

                        TextInput::make('total')
                            ->disabled()
                            ->dehydrated()
                            ->prefix('$')
                            ->label('Total'),
                    ])->columns(3),

                Section::make('Order Timeline')
                    ->description('🔒 Timestamps are set automatically')
                    ->schema([
                        DateTimePicker::make('confirmed_at')
                            ->disabled()
                            ->dehydrated()
                            ->label('Confirmed At')
                            ->native(false),

                        DateTimePicker::make('prepared_at')
                            ->disabled()
                            ->dehydrated()
                            ->label('Prepared At')
                            ->native(false),

                        DateTimePicker::make('ready_at')
                            ->disabled()
                            ->dehydrated()
                            ->label('Ready At')
                            ->native(false),

                        DateTimePicker::make('completed_at')
                            ->disabled()
                            ->dehydrated()
                            ->label('Completed At')
                            ->native(false),

                        DateTimePicker::make('cancelled_at')
                            ->disabled()
                            ->dehydrated()
                            ->label('Cancelled At')
                            ->native(false),
                    ])->columns(2),
            ]);
    }
}
