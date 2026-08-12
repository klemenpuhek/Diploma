<?php

namespace App\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;

class Court extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'courts';

    protected $fillable = [
        'number',
        'imagePath',
    ];

    public function reservations()
    {
        return Reservation::where('court', new ObjectId($this->getKey()));
    }
}