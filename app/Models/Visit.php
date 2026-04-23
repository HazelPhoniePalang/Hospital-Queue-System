<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_date',
        'notes',
        'diagnosis',
        'status',
        'patient_id',
        'doctor_id',
        'queue_id',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
        ];
    }

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'visit_id';
    }

    public function patient(): BelongsTo
    {
        $ownerKey = (new Patient())->getKeyName();
        return $this->belongsTo(Patient::class, 'patient_id', $ownerKey);
    }

    public function doctor(): BelongsTo
    {
        $ownerKey = (new User())->getKeyName();
        return $this->belongsTo(User::class, 'doctor_id', $ownerKey);
    }

    public function queue(): BelongsTo
    {
        $ownerKey = (new QueueEntry())->getKeyName();
        return $this->belongsTo(QueueEntry::class, 'queue_id', $ownerKey);
    }
}
