<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','day','time_s','facility_id','time_end'];

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function Facility()
    {
        return $this->belongsTo('App\Models\Facility');
    }
}