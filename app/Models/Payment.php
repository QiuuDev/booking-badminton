<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id',
        'proof_image',
        'status',
    ];

    /**
     * Payment belongs to a booking.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
