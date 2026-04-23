<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'service_name',
        'average_duration',
        'cost',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'service_id';
    }

    public function department(): BelongsTo
    {
        $ownerKey = (new Department())->getKeyName();
        return $this->belongsTo(Department::class, 'department_id', $ownerKey);
    }

    public function queues(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'service_id', $this->getKeyName());
    }
}
