<div>
    <div wire:poll.5s="loadOrders" class="min-h-screen bg-gray-50 p-6">
        
        <!-- Header with Stats and Filters -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-900">Kitchen Display</h1>
                
                @if($pendingOrders->count() > 0)
                <span class="flex items-center gap-2 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold animate-pulse">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    {{ $pendingOrders->count() }} Pending
                </span>
                @endif
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-2">
                <button 
                    wire:click="setFilter('all')"
                    class="px-4 py-2 rounded-lg font-medium transition {{ $filterStatus === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    All Orders
                </button>
                <button 
                    wire:click="setFilter('pending')"
                    class="px-4 py-2 rounded-lg font-medium transition {{ $filterStatus === 'pending' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Pending
                </button>
                <button 
                    wire:click="setFilter('preparing')"
                    class="px-4 py-2 rounded-lg font-medium transition {{ $filterStatus === 'preparing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Preparing
                </button>
                <button 
                    wire:click="setFilter('ready')"
                    class="px-4 py-2 rounded-lg font-medium transition {{ $filterStatus === 'ready' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Ready
                </button>
            </div>
        </div>

        <!-- Kitchen Board -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- PENDING Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-4 py-3 bg-orange-100 rounded-lg">
                    <h2 class="text-lg font-bold text-orange-900">PENDING</h2>
                    <span class="px-3 py-1 bg-orange-200 text-orange-900 rounded-full text-sm font-bold">
                        {{ $pendingOrders->count() }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    @forelse($pendingOrders as $order)
                        @include('livewire.kitchen.partials.order-card', [
                            'order' => $order,
                            'statusColor' => 'orange',
                            'showAlert' => $order->created_at->diffInMinutes(now()) > 15
                        ])
                    @empty
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-gray-500">No pending orders</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- PREPARING Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-4 py-3 bg-blue-100 rounded-lg">
                    <h2 class="text-lg font-bold text-blue-900">PREPARING</h2>
                    <span class="px-3 py-1 bg-blue-200 text-blue-900 rounded-full text-sm font-bold">
                        {{ $preparingOrders->count() }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    @forelse($preparingOrders as $order)
                        @include('livewire.kitchen.partials.order-card', [
                            'order' => $order,
                            'statusColor' => 'blue',
                            'showAlert' => $order->preparing_at && $order->preparing_at->diffInMinutes(now()) > 20
                        ])
                    @empty
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <p class="mt-2 text-gray-500">No orders in preparation</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- READY Column -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-4 py-3 bg-green-100 rounded-lg">
                    <h2 class="text-lg font-bold text-green-900">READY</h2>
                    <span class="px-3 py-1 bg-green-200 text-green-900 rounded-full text-sm font-bold">
                        {{ $readyOrders->count() }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    @forelse($readyOrders as $order)
                        @include('livewire.kitchen.partials.order-card', [
                            'order' => $order,
                            'statusColor' => 'green',
                            'showAlert' => false
                        ])
                    @empty
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-gray-500">No ready orders</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Order Detail Modal -->
        @if($showDetailModal && $selectedOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeDetailModal">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold">Order #{{ $selectedOrder->order_number }}</h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Table</p>
                            <p class="font-semibold">{{ $selectedOrder->table->table_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Customer</p>
                            <p class="font-semibold">{{ $selectedOrder->customer_name ?? 'Walk-in' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Time</p>
                            <p class="font-semibold">{{ $selectedOrder->created_at->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Time Elapsed</p>
                            <p class="font-semibold">{{ $selectedOrder->created_at->diffForHumans(null, true) }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div>
                        <h4 class="font-semibold mb-3">Order Items</h4>
                        <div class="space-y-3">
                            @foreach($selectedOrder->items as $item)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="font-medium">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                    <span class="font-semibold">${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                                
                                @if($item->special_instructions)
                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm">
                                    <p class="font-medium text-yellow-900">Special Instructions:</p>
                                    <p class="text-yellow-800">{{ $item->special_instructions }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Notes -->
                    @if($selectedOrder->notes)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="font-semibold text-blue-900 mb-1">Order Notes:</p>
                        <p class="text-blue-800">{{ $selectedOrder->notes }}</p>
                    </div>
                    @endif

                    <!-- Status Timeline -->
                    <div>
                        <h4 class="font-semibold mb-3">Status History</h4>
                        <div class="space-y-2">
                            @if($selectedOrder->created_at)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium">Order Placed</p>
                                    <p class="text-xs text-gray-500">{{ $selectedOrder->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($selectedOrder->confirmed_at)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium">Confirmed</p>
                                    <p class="text-xs text-gray-500">{{ $selectedOrder->confirmed_at->format('h:i A') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($selectedOrder->preparing_at)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium">Started Preparing</p>
                                    <p class="text-xs text-gray-500">{{ $selectedOrder->preparing_at->format('h:i A') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($selectedOrder->ready_at)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium">Ready for Pickup</p>
                                    <p class="text-xs text-gray-500">{{ $selectedOrder->ready_at->format('h:i A') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>${{ number_format($selectedOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm">Updating...</span>
        </div>
    </div>
</div>
