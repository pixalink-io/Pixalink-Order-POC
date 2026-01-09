<div>
    @if($isOpen && $menuItem)
    <!-- Modal Overlay -->
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <!-- Close Button -->
                <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-10">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Content -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <!-- Item Image & Basic Info -->
                    <div class="mb-6">
                        @if($menuItem->image)
                        <img src="{{ Storage::url($menuItem->image) }}" alt="{{ $menuItem->name }}" class="w-full h-48 object-cover rounded-lg mb-4">
                        @endif
                        
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $menuItem->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $menuItem->category->name }}</p>
                            </div>
                            <p class="text-2xl font-bold text-indigo-600">${{ number_format($menuItem->price, 2) }}</p>
                        </div>
                        
                        @if($menuItem->description)
                        <p class="mt-2 text-gray-600">{{ $menuItem->description }}</p>
                        @endif
                    </div>

                    <!-- Error Messages -->
                    @if(!empty($errors))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Option Groups -->
                    @foreach($menuItem->optionGroups as $group)
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">
                            {{ $group->name }}
                            @if($group->is_required)
                            <span class="text-red-500">*</span>
                            @endif
                        </h4>
                        
                        @if($group->type === 'single')
                        <!-- Radio Buttons -->
                        <div class="space-y-2">
                            @foreach($group->options as $option)
                            @php
                                $isSelected = $this->isOptionSelected($group->id, $option->id);
                            @endphp
                            <label 
                                wire:click="selectOption({{ $group->id }}, {{ $option->id }})"
                                class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ !$option->is_available ? 'opacity-50 cursor-not-allowed' : '' }} {{ $isSelected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}"
                            >
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-4 w-4 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }}">
                                        @if($isSelected)
                                        <div class="h-2 w-2 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-gray-900">
                                        {{ $option->name }}
                                        @if(!$option->is_available)
                                        <span class="text-xs text-red-500">(Unavailable)</span>
                                        @endif
                                    </span>
                                </div>
                                @if($option->price_adjustment != 0)
                                <span class="text-sm {{ $option->price_adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $option->price_adjustment > 0 ? '+' : '' }}${{ number_format($option->price_adjustment, 2) }}
                                </span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @else
                        <!-- Checkboxes -->
                        <div class="space-y-2">
                            @foreach($group->options as $option)
                            @php
                                $isSelected = $this->isOptionSelected($group->id, $option->id);
                            @endphp
                            <label 
                                wire:click="selectOption({{ $group->id }}, {{ $option->id }})"
                                class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ !$option->is_available ? 'opacity-50 cursor-not-allowed' : '' }} {{ $isSelected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}"
                            >
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-4 w-4 rounded border-2 flex items-center justify-center {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }}">
                                        @if($isSelected)
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-gray-900">
                                        {{ $option->name }}
                                        @if(!$option->is_available)
                                        <span class="text-xs text-red-500">(Unavailable)</span>
                                        @endif
                                    </span>
                                </div>
                                @if($option->price_adjustment != 0)
                                <span class="text-sm {{ $option->price_adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $option->price_adjustment > 0 ? '+' : '' }}${{ number_format($option->price_adjustment, 2) }}
                                </span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @endif
                        
                        @if($group->min_selections > 0 || $group->max_selections)
                        <p class="text-xs text-gray-500 mt-1">
                            @if($group->min_selections > 0 && $group->max_selections)
                            Select {{ $group->min_selections }} to {{ $group->max_selections }} option(s)
                            @elseif($group->min_selections > 0)
                            Select at least {{ $group->min_selections }} option(s)
                            @elseif($group->max_selections)
                            Select up to {{ $group->max_selections }} option(s)
                            @endif
                        </p>
                        @endif
                    </div>
                    @endforeach

                    <!-- Special Instructions -->
                    <div class="mb-6">
                        <label for="special-instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions (Optional)
                        </label>
                        <textarea 
                            id="special-instructions"
                            wire:model="specialInstructions" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., No onions, extra sauce..."
                        ></textarea>
                    </div>

                    <!-- Quantity & Add to Cart -->
                    <div class="flex items-center justify-between gap-4">
                        <!-- Quantity Selector -->
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button 
                                wire:click="decrementQuantity" 
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="px-6 py-2 text-lg font-semibold">{{ $quantity }}</span>
                            <button 
                                wire:click="incrementQuantity" 
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <!-- Add to Cart Button -->
                        <button 
                            wire:click="addToCart" 
                            wire:loading.attr="disabled"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="addToCart">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="addToCart">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span>Add to Cart - ${{ number_format($this->itemTotal, 2) }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
