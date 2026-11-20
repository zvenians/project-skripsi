<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityManager extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'manager_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'facility_manager_id');
    }
}
