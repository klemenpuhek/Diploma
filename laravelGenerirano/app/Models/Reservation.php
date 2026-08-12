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

    public const OPEN_HOUR = 8;
    public const CLOSE_HOUR = 22;

    // Named "courtModel" (not "court") because "court" is already the raw
    // foreign key attribute - reusing the name causes infinite/undefined
    // property resolution when accessing $reservation->court.
    public function courtModel()
    {
        return $this->belongsTo(Court::class, 'court');
    }

    /**
     * Existing rows store hours either as "8" or "08:00" - normalize both to an int hour.
     */
    public static function normalizeHour($value): int
    {
        return (int) explode(':', (string) $value)[0];
    }
}
