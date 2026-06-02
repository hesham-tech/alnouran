<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ppm',
        'quantity',
        'cont_hours',
        'actual_time',
        'slices_ton',
        'shift',
        'note',
        'typePreparation_id',
        'station_id',
        'user_id'
    ];

    public function typePreparation()
    {
        return $this->belongsTo(TypePreparation::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
