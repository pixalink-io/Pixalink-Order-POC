<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Icon & Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600">Thank you for your order, {{ $order->customer_name }}!</p>
        </div>

        <!-- Order Number Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Order Number</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-600 mt-4">Table {{ $order->table_number }}</p>
            </div>
        </div>

        <!-- Status Message -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <p class="text-indigo-900 text-center">
                <span class="font-semibold">Your order is being prepared.</span><br>
                <span class="text-sm">We'll bring it to your table shortly!</span>
            </p>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>

            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="flex justify-between text-sm pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">
                            {{ $item->menu_item_name }} × {{ $item->quantity }}
                        </p>
                        
                        <!-- Selected Options -->
                        @if(!empty($item->selected_options))
                        <div class="mt-1 space-y-0.5">
                            @foreach($item->selected_options as $groupId => $optionIds)
                                @php
                                    $optionGroup = \App\Models\OptionGroup::find($groupId);
                                    if (!$optionGroup) continue;
                                    
                                    $selectedOptionsList = is_array($optionIds) ? $optionIds : [$optionIds];
                                    $optionNames = \App\Models\Option::whereIn('id', $selectedOptionsList)->pluck('name')->toArray();
                                @endphp
                                @if(!empty($optionNames))
                                <p class="text-xs text-gray-600">• {{ implode(', ', $optionNames) }}</p>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        @if($item->special_instructions)
                        <p class="text-xs text-gray-500 italic mt-1">Note: {{ $item->special_instructions }}</p>
                        @endif
                    </div>
                    <p class="font-medium text-gray-900 ml-4">
                        ${{ number_format($item->item_total, 2) }}
                    </p>
                </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="border-t border-gray-200 mt-6 pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Tax</span>
                    <span>${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span class="text-indigo-600">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a 
                href="{{ route('menu.index') }}"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 px-6 rounded-lg transition"
            >
                Order More
            </a>
        </div>
    </div>
</div>
