<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Order
 *
 * Core transaction table. Lifecycle:
 *   pending → confirmed → preparing → ready → completed
 *                    └──────────────────────→ cancelled
 *
 * @property int         $id
 * @property string      $order_number   e.g. KD-250601-0001
 * @property int         $user_id
 * @property string      $status         pending|confirmed|preparing|ready|completed|cancelled
 * @property string      $type           dine_in|takeaway|delivery
 * @property float       $subtotal
 * @property float       $discount_amount
 * @property float       $tax_amount
 * @property float       $total_amount
 * @property string      $payment_method cash|qris|unpaid
 * @property \Carbon\Carbon|null $paid_at
 * @property int|null    $table_number
 * @property string|null $customer_notes
 * @property string|null $cashier_notes
 * @property int|null    $processed_by
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------
    // Constants
    // -------------------------------------------------------

    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PENDING         = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY     = 'ready';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_DINE_IN  = 'dine_in';
    const TYPE_TAKEAWAY = 'takeaway';
    const TYPE_DELIVERY = 'delivery';

    const PAYMENT_CASH     = 'cash';
    const PAYMENT_QRIS     = 'qris';
    const PAYMENT_UNPAID   = 'unpaid';

    // -------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'type',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'paid_at',
        'table_number',
        'customer_name',
        'phone_number',
        'customer_notes',
        'cashier_notes',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'paid_at'         => 'datetime',
            'table_number'    => 'integer',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The customer who placed this order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The cashier or admin who processed this order.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * All line items belonging to this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // -------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
            self::STATUS_READY,
        ]);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForCustomer(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('created_at', '>=', today())
                     ->where('created_at', '<', today()->addDay());
    }

    // -------------------------------------------------------
    // Status Helpers
    // -------------------------------------------------------

    public function isPendingPayment(): bool { return $this->status === self::STATUS_PENDING_PAYMENT; }
    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isConfirmed(): bool  { return $this->status === self::STATUS_CONFIRMED; }
    public function isPreparing(): bool  { return $this->status === self::STATUS_PREPARING; }
    public function isReady(): bool      { return $this->status === self::STATUS_READY; }
    public function isCompleted(): bool  { return $this->status === self::STATUS_COMPLETED; }
    public function isCancelled(): bool  { return $this->status === self::STATUS_CANCELLED; }
    public function isActive(): bool     { return in_array($this->status, [self::STATUS_PENDING_PAYMENT, self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PREPARING, self::STATUS_READY]); }
    public function isPaid(): bool       { return $this->paid_at !== null; }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Human-readable status label in Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PENDING         => 'Menunggu',
            self::STATUS_CONFIRMED       => 'Dikonfirmasi',
            self::STATUS_PREPARING       => 'Diproses',
            self::STATUS_READY           => 'Siap Diambil',
            self::STATUS_COMPLETED       => 'Selesai',
            self::STATUS_CANCELLED       => 'Dibatalkan',
            default                      => ucfirst($this->status),
        };
    }

    /**
     * Formatted total in IDR.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}
