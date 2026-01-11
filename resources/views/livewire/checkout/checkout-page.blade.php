<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="mt-2 text-sm text-gray-600">Complete your order</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Customer Details Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Details</h2>

                    <form wire:submit.prevent="placeOrder" class="space-y-6">
                        <!-- Customer Name -->
                        <div>
                            <label for="customerName" class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="customerName"
                                wire:model="customerName"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('customerName') border-red-500 @enderror"
                                placeholder="Enter your name"
                            >
                            @error('customerName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="customerPhone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-gray-400">(Optional)</span>
                            </label>
                            <input 
                                type="tel" 
                                id="customerPhone"
                                wire:model="customerPhone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('customerPhone') border-red-500 @enderror"
                                placeholder="e.g., 0123456789"
                            >
                            @error('customerPhone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Table Number -->
                        <div>
                            <label for="tableNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                Table Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="tableNumber"
                                wire:model="tableNumber"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tableNumber') border-red-500 @enderror"
                                placeholder="e.g., A5 or 12"
                            >
                            @error('tableNumber')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Special Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Notes <span class="text-gray-400">(Optional)</span>
                            </label>
                            <textarea 
                                id="notes"
                                wire:model="notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-500 @enderror"
                                placeholder="Any special requests or dietary restrictions..."
                            ></textarea>
                            @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button (Mobile Only - hidden on desktop) -->
                        <div class="lg:hidden">
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                                <span wire:loading wire:target="placeOrder">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">
                                    {{ $item['name'] }} × {{ $item['quantity'] }}
                                </p>
                                
                                <!-- Selected Options -->
                                @if(!empty($item['selected_options']))
                                <div class="mt-1 space-y-0.5">
                                    @foreach($item['selected_options'] as $groupId => $optionIds)
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

                                @if($item['special_instructions'])
                                <p class="text-xs text-gray-500 italic mt-1">Note: {{ $item['special_instructions'] }}</p>
                                @endif
                            </div>
                            <p class="font-medium text-gray-900 ml-4">
                                ${{ number_format($item['item_total'], 2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($totals['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tax (10%)</span>
                            <span>${{ number_format($totals['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-indigo-600">${{ number_format($totals['total'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Submit Button (Desktop Only) -->
                    <button 
                        wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        class="hidden lg:flex w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                        <span wire:loading wire:target="placeOrder" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
