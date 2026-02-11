<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhook extends Model
{
    protected $table = 'payment_webhooks';

    protected $fillable = [
        'id',
        'transaction_id',
        'payment_provider',
        'webhook_type',
        'payload',
        'signature',
        'is_verified',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
