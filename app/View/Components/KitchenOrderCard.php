<?php

namespace App\View\Components;

use App\Models\Order;
use Illuminate\View\Component;
use Illuminate\View\View;

class KitchenOrderCard extends Component
{
    public function __construct(
        public Order $order,
        public string $statusColor = 'gray',
        public bool $showAlert = false
    ) {
    }

    public function render(): View
    {
        return view('components.kitchen-order-card');
    }

    public function getTimeElapsed(): string
    {
        $timestampField = match ($this->order->status) {
            'preparing' => 'preparing_at',
            'ready' => 'ready_at',
            default => 'created_at',
        };

        $timestamp = $this->order->{$timestampField} ?? $this->order->created_at;

        return $timestamp->diffForHumans(null, true);
    }

    public function getNextStatus(): ?string
    {
        return match ($this->order->status) {
            'pending', 'confirmed' => 'preparing',
            'preparing' => 'ready',
            'ready' => 'completed',
            default => null,
        };
    }

    public function getNextStatusLabel(): string
    {
        return match ($this->getNextStatus()) {
            'preparing' => 'Start Preparing',
            'ready' => 'Mark Ready',
            'completed' => 'Complete',
            default => 'Update',
        };
    }
}
