<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Casts\ObjectId;
use MongoDB\Laravel\Eloquent\Model;

class Reservation extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'reservations';

    protected $fillable = [
        'court',
        'name',
        'surname',
        'email',
        'date',
        'startingHour',
        'endingHour',
    ];

    protected $casts = [
        'court' => ObjectId::class,
    ];

    public function courtModel()
    {
        return $this->belongsTo(Court::class, 'court'); // spet relacija na property 'court'
    }
}