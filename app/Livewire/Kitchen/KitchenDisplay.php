<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;

#[Title('Kitchen Display')]
class KitchenDisplay extends Component
{
    public $pendingOrders = [];
    public $preparingOrders = [];
    public $readyOrders = [];
    public $selectedOrder = null;
    public $showDetailModal = false;
    public $filterStatus = 'all';

    public function mount(): void
    {
        $this->loadOrders();
    }

    #[On('orderUpdated')]
    public function loadOrders(): void
    {
        $query = Order::with(['orderItems.menuItem'])  // ✅ Removed 'table'
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready']);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $orders = $query->latest('created_at')->get();

        $this->pendingOrders = $orders->whereIn('status', ['pending', 'confirmed'])->values();
        $this->preparingOrders = $orders->where('status', 'preparing')->values();
        $this->readyOrders = $orders->where('status', 'ready')->values();
    }

    public function updateOrderStatus(int $orderId, string $newStatus): void
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => $newStatus,
            $this->getStatusTimestampField($newStatus) => now(),
        ]);

        $this->loadOrders();
        $this->dispatch('orderUpdated');

        session()->flash('message', "Order #{$order->order_number} marked as {$newStatus}");
    }

    protected function getStatusTimestampField(string $status): string
    {
        return match ($status) {
            'confirmed' => 'confirmed_at',
            'preparing' => 'preparing_at',
            'ready' => 'ready_at',
            'completed' => 'completed_at',
            default => 'updated_at',
        };
    }

    public function showOrderDetails(int $orderId): void
    {
        $this->selectedOrder = Order::with(['orderItems.menuItem'])  // ✅ Removed 'table'
            ->findOrFail($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.kitchen.kitchen-display');
    }
}
