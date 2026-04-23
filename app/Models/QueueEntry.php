<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class QueueEntry extends Model
{
    use HasFactory;

    protected $table = 'queues';

    protected $fillable = [
        'queue_no',
        'priority_level',
        'status',
        'called_at',
        'completed_at',
        'patient_id',
        'department_id',
        'service_id',
        'symptoms',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'queue_id';
    }

    public function patient(): BelongsTo
    {
        $ownerKey = (new Patient)->getKeyName();

        return $this->belongsTo(Patient::class, 'patient_id', $ownerKey);
    }

    public function department(): BelongsTo
    {
        $ownerKey = (new Department)->getKeyName();

        return $this->belongsTo(Department::class, 'department_id', $ownerKey);
    }

    public function service(): BelongsTo
    {
        $ownerKey = (new Service)->getKeyName();

        return $this->belongsTo(Service::class, 'service_id', $ownerKey);
    }

    public function visit(): HasOne
    {
        return $this->hasOne(Visit::class, 'queue_id', $this->getKeyName());
    }
}
