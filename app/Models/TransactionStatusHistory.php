<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatusHistory extends Model
{
    protected $table = 'transaction_status_history';

    protected $fillable = [
        'id',
        'transaction_id',
        'from_status',
        'to_status',
        'changed_by',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
