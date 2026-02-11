<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderWebhook extends Model
{
    protected $table = 'provider_webhooks';

    protected $fillable = [
        'id',
        'transaction_id',
        'provider_code',
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
