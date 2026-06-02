<?php

namespace App\Models;

use App\Models\Preparation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePreparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'station_id',
        'user_id'
    ];
    public function latestPreparationActual()
    {
        return $this->hasOne(Preparation::class, 'typePreparation_id')->latest();
    }
    public function preparations()
    {
        return $this->hasMany(Preparation::class, 'typePreparation_id')->orderBy('created_at', 'desc');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function station()
    {
        return $this->belongsTo(Station::class);
    }
    
   
}

