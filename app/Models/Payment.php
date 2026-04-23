<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id',
        'patient_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'payment_id';
    }

    public function queue(): BelongsTo
    {
        $ownerKey = (new QueueEntry())->getKeyName();
        return $this->belongsTo(QueueEntry::class, 'queue_id', $ownerKey);
    }

    public function patient(): BelongsTo
    {
        $ownerKey = (new Patient())->getKeyName();
        return $this->belongsTo(Patient::class, 'patient_id', $ownerKey);
    }
}
