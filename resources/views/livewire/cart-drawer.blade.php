<div>
    <!-- Cart Drawer -->
    <div class="fixed inset-0 overflow-hidden z-50 {{ $isOpen ? '' : 'pointer-events-none' }}" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="absolute inset-0 overflow-hidden">
            <div 
                class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity {{ $isOpen ? 'opacity-100' : 'opacity-0' }}"
                wire:click="closeDrawer"
            ></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="w-screen max-w-md transform transition {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}">
                    <div class="flex h-full flex-col bg-white shadow-xl">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-6 border-b">
                            <h2 class="text-lg font-semibold text-gray-900">Shopping Cart</h2>
                            <button wire:click="closeDrawer" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Flash Messages -->
                        @if(session('cart-message'))
                        <div class="mx-4 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                            {{ session('cart-message') }}
                        </div>
                        @endif

                        <!-- Cart Items -->
                        <div class="flex-1 overflow-y-auto px-4 py-6">
                            @if(empty($cart))
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
                                <p class="mt-1 text-sm text-gray-500">Start adding some delicious items!</p>
                            </div>
                            @else
                            <div class="space-y-4">
                                @foreach($cart as $item)
                                <div class="flex gap-4 bg-gray-50 p-4 rounded-lg">
                                    <!-- Item Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($item['image'])
                                        <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Item Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ $item['category'] }}</p>
                                        
                                        <!-- Selected Options -->
                                        @if(!empty($item['selected_options']))
                                        <div class="mt-1">
                                            @foreach($item['selected_options'] as $groupId => $optionIds)
                                                @php
                                                    $optionGroup = \App\Models\OptionGroup::find($groupId);
                                                    if (!$optionGroup) continue;
                                                    
                                                    $selectedOptionsList = is_array($optionIds) ? $optionIds : [$optionIds];
                                                    $optionNames = \App\Models\Option::whereIn('id', $selectedOptionsList)->pluck('name')->toArray();
                                                @endphp
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-medium">{{ $optionGroup->name }}:</span> {{ implode(', ', $optionNames) }}
                                                </p>
                                            @endforeach
                                        </div>
                                        @endif
                                        
                                        @if($item['special_instructions'])
                                        <p class="text-xs text-gray-600 mt-1">
                                            <span class="font-medium">Note:</span> {{ $item['special_instructions'] }}
                                        </p>
                                        @endif

                                        <!-- Price & Quantity -->
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="flex items-center border border-gray-300 rounded">
                                                <button 
                                                    wire:click="updateQuantity('{{ $item['id'] }}', {{ $item['quantity'] - 1 }})"
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <span class="px-3 py-1 text-sm font-medium">{{ $item['quantity'] }}</span>
                                                <button 
                                                    wire:click="updateQuantity('{{ $item['id'] }}', {{ $item['quantity'] + 1 }})"
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <p class="text-sm font-semibold text-gray-900">
                                                ${{ number_format($item['item_total'], 2) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button 
                                        wire:click="removeItem('{{ $item['id'] }}')"
                                        class="self-start text-red-500 hover:text-red-700"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <!-- Clear Cart Button -->
                            <button 
                                wire:click="clearCart"
                                wire:confirm="Are you sure you want to clear your cart?"
                                class="mt-4 w-full text-sm text-red-600 hover:text-red-800 underline"
                            >
                                Clear Cart
                            </button>
                            @endif
                        </div>

                        <!-- Footer with Totals and Checkout -->
                        @if(!empty($cart))
                        <div class="border-t px-4 py-6">
                            <!-- Totals -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">${{ number_format($totals['subtotal'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax (10%)</span>
                                    <span class="font-medium">${{ number_format($totals['tax'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t pt-2">
                                    <span>Total</span>
                                    <span class="text-indigo-600">${{ number_format($totals['total'], 2) }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <a 
                                href="{{ route('checkout.index') }}"
                                class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                            >
                                Proceed to Checkout
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
