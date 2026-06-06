<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\OrderItem
 *
 * Line items for an order. Stores a price snapshot at order time.
 *
 * IMPORTANT: `subtotal` is a MySQL STORED GENERATED column (quantity × unit_price).
 * It is NOT in $fillable — never set it manually, it is computed by the database.
 *
 * @property int         $id
 * @property int         $order_id
 * @property int|null    $menu_id        nullable — survives menu soft/hard delete
 * @property string      $menu_name      snapshot of menu.name at order time
 * @property string|null $menu_category  snapshot of menu.category at order time
 * @property int         $quantity
 * @property float       $unit_price     snapshot of menu.price at order time
 * @property float       $subtotal       GENERATED: quantity × unit_price
 * @property string|null $notes          per-item special instructions
 */
class OrderItem extends Model
{
    use HasFactory;

    // -------------------------------------------------------
    // Mass Assignment
    // NOTE: 'subtotal' is intentionally excluded — it is a
    // stored generated column computed by MySQL automatically.
    // -------------------------------------------------------

    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_name',
        'menu_category',
        'quantity',
        'unit_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The order this item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The original menu item (nullable — survives menu deletion).
     * Use menu_name / menu_category snapshots for display instead
     * of relying on this relationship for financial data.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class)->withTrashed();
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Formatted unit price in IDR.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    /**
     * Formatted subtotal in IDR.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Convenience factory method — captures menu snapshot automatically.
     * Usage: OrderItem::fromMenu($menu, $quantity, $notes)
     */
    public static function fromMenu(Menu $menu, int $quantity = 1, ?string $notes = null): array
    {
        return [
            'menu_id'       => $menu->id,
            'menu_name'     => $menu->name,
            'menu_category' => $menu->category,
            'quantity'      => $quantity,
            'unit_price'    => $menu->price,
            'notes'         => $notes,
        ];
    }
}
