<x-app-layout>
    <!-- Add to Cart Modal -->
    <livewire:add-to-cart-modal />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Category Filter -->
        <div class="mb-6">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <!-- All Categories Button -->
                <a href="{{ route('menu.index') }}" 
                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition {{ !$selectedCategory ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    All Items
                </a>

                <!-- Category Buttons -->
                @foreach($categories as $category)
                    <a href="{{ route('menu.index', ['category' => $category->id]) }}" 
                       class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedCategory == $category->id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Menu Items Grid -->
        @if($menuItems->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No items available</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for delicious options!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($menuItems as $item)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden group {{ !$item->is_available ? 'opacity-60' : '' }}">
                        <!-- Item Image -->
                        <div class="aspect-video bg-gray-200 overflow-hidden relative">
                            @if($item->image)
                                <img src="{{ Storage::url($item->image) }}" 
                                     alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Featured Badge -->
                            @if($item->is_featured)
                                <div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Featured
                                </div>
                            @endif
                            
                            <!-- Unavailable Badge -->
                            @if(!$item->is_available)
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    Unavailable
                                </div>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item->category->name }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-lg font-bold text-indigo-600">
                                        ${{ number_format($item->price, 2) }}
                                    </p>
                                </div>
                            </div>

                            @if($item->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                    {{ $item->description }}
                                </p>
                            @endif

                            <!-- Options Badge -->
                            @if($item->optionGroups->isNotEmpty())
                                <div class="mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
                                        </svg>
                                        Customizable
                                    </span>
                                </div>
                            @endif

                            <!-- Add to Cart Button -->
                            <button 
                                onclick="Livewire.dispatch('openAddToCartModal', { menuItemId: {{ $item->id }} })"
                                class="w-full font-medium py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 {{ $item->is_available ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                {{ !$item->is_available ? 'disabled' : '' }}
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $item->is_available ? 'Add to Cart' : 'Unavailable' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Custom Styles for Scrollbar Hide -->
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
