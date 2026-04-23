<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role_name',
        'description',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): ?string
    {
        if (array_key_exists('name', $this->attributes)) {
            return $this->attributes['name'];
        }

        return $this->attributes['role_name'] ?? null;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
