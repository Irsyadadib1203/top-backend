<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'invoice_number',
        'idempotency_key',
        'customer_id',
        'customer_phone',
        'game_id',
        'nominal_id',
        'provider_id',
        'base_price',
        'selling_price',
        'admin_fee',
        'total_amount',
        'status',
        'status_version',
        'previous_status',
        'payment_method',
        'payment_reference',
        'payment_verified_at',
        'provider_order_id',
        'provider_status',
        'provider_message',
        'provider_callback_data',
        'retry_count',
        'last_retry_at',
        'max_retries',
        'completed_at',
    ];

    protected $casts = [
        'provider_callback_data' => 'array',
        'payment_verified_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function nominal()
    {
        return $this->belongsTo(Nominal::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(TransactionStatusHistory::class);
    }
}
