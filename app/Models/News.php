<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','desc_n','time','category_n','facility_id'];

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function Facility()
    {
        return $this->belongsTo('App\Models\Facility');
    }
}