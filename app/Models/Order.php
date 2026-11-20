<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['detil', 'user_id', 'organization_id', 'facility_id', 'date_end', 'pesan', 'selesai', 'status_order', 'decision_reason'];

    protected $casts = ['date_end' => 'datetime', 'selesai' => 'datetime', 'status_order' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
