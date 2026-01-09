<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>

<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('menu.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Menu
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-6">Delivery Information</h2>

                        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Delivery
                                    Address</label>
                                <textarea id="address" name="address" rows="3" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="cash" {{ old('payment_method') === 'cash' ? 'checked' : '' }} required class="mr-2">
                                        <span>Cash on Delivery</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="card" {{ old('payment_method') === 'card' ? 'checked' : '' }} class="mr-2">
                                        <span>Card on Delivery</span>
                                    </label>
                                </div>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Order Notes
                                    (Optional)</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Any special requests?"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-4">
                            @foreach($cart as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                    <span class="font-medium">${{ number_format($item['item_total'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2">
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

                        <div class="mt-4 text-xs text-gray-500">
                            <p>{{ $totals['item_count'] }} {{ Str::plural('item', $totals['item_count']) }} in cart</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
