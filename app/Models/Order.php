<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'table_number',
        'notes',
        'status',
        'subtotal',
        'tax',
        'total',
        'placed_at',
        'confirmed_at',
        'prepared_at',
        'ready_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Create order from cart data
     */
    public static function createFromCart(array $cartItems, array $customerDetails): self
    {
        return DB::transaction(function () use ($cartItems, $customerDetails) {
            // Generate unique order number
            $orderNumber = self::generateOrderNumber();

            // Calculate totals
            $subtotal = array_sum(array_column($cartItems, 'item_total'));
            $tax = $subtotal * 0.10; // 10% tax
            $total = $subtotal + $tax;

            // Create order
            $order = self::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerDetails['customer_name'],
                'customer_phone' => $customerDetails['customer_phone'] ?? null,
                'customer_email' => $customerDetails['customer_email'] ?? null,
                'table_number' => $customerDetails['table_number'],
                'notes' => $customerDetails['notes'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pending',
                'placed_at' => now(),
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'menu_item_id' => $cartItem['menu_item_id'],
                    'menu_item_name' => $cartItem['name'],
                    'menu_item_price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'selected_options' => $cartItem['selected_options'] ?? [],
                    'options_total' => $cartItem['options_total'] ?? 0,
                    'item_total' => $cartItem['item_total'],
                    'special_instructions' => $cartItem['special_instructions'] ?? null,
                ]);
            }

            return $order->fresh(['orderItems']);
        });
    }

    // Status transition methods
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function startPreparing(): void
    {
        $this->update([
            'status' => 'preparing',
            'prepared_at' => now(),
        ]);
    }

    public function markReady(): void
    {
        $this->update([
            'status' => 'ready',
            'ready_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(3));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    // Scope for filtering by status
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Scope for active orders (not completed or cancelled)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    // Helper to get status label
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    // Helper to get status color
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'gray',
            'confirmed' => 'blue',
            'preparing' => 'yellow',
            'ready' => 'green',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
