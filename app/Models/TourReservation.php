<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourReservation extends Model
{
    protected $fillable = [
        'tour_id',
        'app_user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'number_of_people',
        'status',
        'notes',
    ];

    /**
     * Get the tour that this reservation belongs to.
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the user that made this reservation.
     */
    public function appUser()
    {
        return $this->belongsTo(AppUser::class);
    }
}
