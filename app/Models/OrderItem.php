<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'menu_item_price',
        'quantity',
        'selected_options',
        'options_total',
        'item_total',
        'special_instructions',
    ];

    protected $casts = [
        'menu_item_price' => 'decimal:2',
        'quantity' => 'integer',
        'selected_options' => 'array',
        'options_total' => 'decimal:2',
        'item_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Calculate item total
    public function calculateTotal(): float
    {
        return ($this->menu_item_price + $this->options_total) * $this->quantity;
    }

    // Format selected options for display
    public function formattedOptions(): string
    {
        if (empty($this->selected_options)) {
            return '';
        }

        $formatted = [];
        foreach ($this->selected_options as $group) {
            $options = implode(', ', array_column($group['options'], 'name'));
            $formatted[] = "{$group['group_name']}: {$options}";
        }

        return implode(' | ', $formatted);
    }
}
