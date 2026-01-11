<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings'; // adjust if your table name differs
    protected $fillable = ['user_id', 'court_id', 'booking_date', 'start_time', 'end_time', 'total_price', 'status'];
    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];
}
