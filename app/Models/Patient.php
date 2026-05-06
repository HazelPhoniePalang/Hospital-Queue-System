<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'contact_no',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function getKeyName(): string
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'patient_id';
    }

    public function user(): BelongsTo
    {
        $ownerKey = (new User())->getKeyName();
        return $this->belongsTo(User::class, 'user_id', $ownerKey);
    }

    public function queues(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'patient_id', $this->getKeyName());
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'patient_id', $this->getKeyName());
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
