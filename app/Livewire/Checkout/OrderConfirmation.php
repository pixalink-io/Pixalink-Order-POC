<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class OrderConfirmation extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['orderItems']);
    }

    public function render()
    {
        return view('livewire.checkout.order-confirmation');
    }
}
