<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['title', 'category', 'image', 'desc', 'manager_id', 'is_master'];

    protected $casts = ['is_master' => 'boolean'];

    public function manager()
    {
        return $this->belongsTo(FacilityManager::class, 'manager_id');
    }
}
