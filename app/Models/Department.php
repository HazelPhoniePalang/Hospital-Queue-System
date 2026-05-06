<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'department_id');
    }

    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class, 'department_id');
    }

    public function queues(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'department_id');
    }
}
