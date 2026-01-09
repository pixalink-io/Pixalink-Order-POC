<div>
    <!-- Cart Drawer -->
    <div class="fixed inset-0 overflow-hidden z-50 {{ $isOpen ? '' : 'pointer-events-none' }}" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true"
         x-data="{ open: @entangle('isOpen') }"
         x-cloak>
        
        <!-- Background overlay -->
        <div class="absolute inset-0 overflow-hidden">
            <div 
                x-show="open"
                x-transition:enter="ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-500 bg-opacity-75"
                wire:click="closeDrawer"
                aria-hidden="true"
            ></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div 
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md"
                >
                    <div class="flex h-full flex-col bg-white shadow-xl">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900" id="slide-over-title">
                                Shopping Cart
                                @if(!empty($cart))
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $totals['item_count'] }} {{ Str::plural('item', $totals['item_count']) }})</span>
                                @endif
                            </h2>
                            <button 
                                wire:click="closeDrawer" 
                                type="button"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg p-1"
                                aria-label="Close cart"
                            >
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Flash Messages -->
                        @if(session('cart-message'))
                        <div class="mx-4 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('cart-message') }}</span>
                        </div>
                        @endif

                        @if(session('cart-error'))
                        <div class="mx-4 mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('cart-error') }}</span>
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
                                <button 
                                    wire:click="closeDrawer"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Browse Menu
                                </button>
                            </div>
                            @else
                            <div class="space-y-4" wire:loading.class="opacity-50">
                                @foreach($cart as $item)
                                <div class="flex gap-4 bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors" wire:key="cart-item-{{ $item['id'] }}">
                                    <!-- Item Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($item['image'])
                                        <img src="{{ Storage::url($item['image']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-full h-full object-cover"
                                             loading="lazy">
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
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ $item['category'] }}</p>
                                        
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
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-medium">{{ $optionGroup->name }}:</span> {{ implode(', ', $optionNames) }}
                                                </p>
                                                @endif
                                            @endforeach
                                        </div>
                                        @endif
                                        
                                        @if($item['special_instructions'])
                                        <p class="text-xs text-gray-600 mt-1 italic">
                                            <span class="font-medium">Note:</span> {{ Str::limit($item['special_instructions'], 50) }}
                                        </p>
                                        @endif

                                        <!-- Price & Quantity -->
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="flex items-center border border-gray-300 rounded">
                                                <button 
                                                    wire:click="updateQuantity('{{ $item['id'] }}', {{ $item['quantity'] - 1 }})"
                                                    wire:loading.attr="disabled"
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    aria-label="Decrease quantity"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <span class="px-3 py-1 text-sm font-medium min-w-[2rem] text-center">{{ $item['quantity'] }}</span>
                                                <button 
                                                    wire:click="updateQuantity('{{ $item['id'] }}', {{ $item['quantity'] + 1 }})"
                                                    wire:loading.attr="disabled"
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    aria-label="Increase quantity"
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
                                        wire:loading.attr="disabled"
                                        wire:confirm="Remove this item from your cart?"
                                        class="self-start text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        aria-label="Remove item"
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
                                wire:confirm="Are you sure you want to clear your entire cart?"
                                wire:loading.attr="disabled"
                                class="mt-4 w-full text-sm text-red-600 hover:text-red-800 underline disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Clear Cart
                            </button>
                            @endif
                        </div>

                        <!-- Footer with Totals and Checkout -->
                        @if(!empty($cart))
                        <div class="border-t border-gray-200 px-4 py-6 bg-gray-50">
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
                                <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2 mt-2">
                                    <span>Total</span>
                                    <span class="text-indigo-600">${{ number_format($totals['total'], 2) }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <a 
                                href="{{ route('checkout.index') }}"
                                class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 flex items-center gap-2">
        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm">Updating cart...</span>
    </div>
</div>
