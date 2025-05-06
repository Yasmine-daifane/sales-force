<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialVisit extends Model
{
    protected $fillable = [
        'user_id',
        'client_name',
        'location',
        'cleaning_type',
        'visit_date',
        'contact',
        'relance_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
