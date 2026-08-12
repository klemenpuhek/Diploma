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
        // The "court" field on reservations is stored as a MongoDB ObjectId,
        // while hasMany() compares it against the string form of this court's
        // _id, so the relation is built manually to match the correct type.
        return Reservation::where('court', new ObjectId($this->getKey()));
    }
}
