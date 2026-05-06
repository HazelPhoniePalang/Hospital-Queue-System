<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Counter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'counters';

    protected $fillable = [
        'department_id',
        'name',
        'assigned_staff_id',
        'current_queue_id',
        'status',
    ];

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'counter_id';
    }

    public function department(): BelongsTo
    {
        $ownerKey = (new Department())->getKeyName();
        return $this->belongsTo(Department::class, 'department_id', $ownerKey);
    }

    public function assignedStaff(): BelongsTo
    {
        $ownerKey = (new User())->getKeyName();
        return $this->belongsTo(User::class, 'assigned_staff_id', $ownerKey);
    }

    public function currentQueue(): BelongsTo
    {
        $ownerKey = (new QueueEntry())->getKeyName();
        return $this->belongsTo(QueueEntry::class, 'current_queue_id', $ownerKey);
    }
}
