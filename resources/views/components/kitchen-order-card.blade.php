@props(['order', 'statusColor' => 'gray', 'showAlert' => false])

<div class="bg-white rounded-lg border-2 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer
           {{ $showAlert ? 'border-red-500 animate-pulse' : 'border-' . $statusColor . '-300' }}"
    wire:click="showOrderDetails({{ $order->id }})">
    <!-- Header -->
    <div class="flex items-start justify-between mb-3">
        <div>
            <h3 class="text-lg font-bold text-gray-900">#{{ $order->order_number }}</h3>
            <p class="text-sm text-gray-600">
                {{ $order->table ? 'Table ' . $order->table->table_number : ($order->customer_name ?? 'Walk-in') }}
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs font-medium text-gray-500">{{ $this->getTimeElapsed() }}</span>
            @if($showAlert)
                <div class="flex items-center gap-1 text-red-600 text-xs font-bold mt-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    URGENT
                </div>
            @endif
        </div>
    </div>

    <!-- Items -->
    <div class="space-y-1 mb-3 max-h-32 overflow-y-auto">
        @foreach($order->items->take(3) as $item)
            <div class="flex justify-between text-sm">
                <span class="text-gray-700">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
            </div>
            @if($item->special_instructions)
                <div class="text-xs text-yellow-700 bg-yellow-50 px-2 py-1 rounded">
                    ⚠️ {{ Str::limit($item->special_instructions, 30) }}
                </div>
            @endif
        @endforeach

        @if($order->items->count() > 3)
            <p class="text-xs text-gray-500 italic">+{{ $order->items->count() - 3 }} more items</p>
        @endif
    </div>

    <!-- Action Button -->
    @if($this->getNextStatus())
        <button wire:click.stop="updateOrderStatus({{ $order->id }}, '{{ $this->getNextStatus() }}')" class="w-full py-2 px-4 rounded-lg font-medium transition-colors
                   {{ $statusColor === 'orange' ? 'bg-orange-600 hover:bg-orange-700 text-white' : '' }}
                   {{ $statusColor === 'blue' ? 'bg-blue-600 hover:bg-blue-700 text-white' : '' }}
                   {{ $statusColor === 'green' ? 'bg-green-600 hover:bg-green-700 text-white' : '' }}">
            {{ $this->getNextStatusLabel() }}
        </button>
    @endif
</div>
