<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\Page;

class KitchenDisplay extends Page
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.resources.orders.pages.kitchen-display';
}
