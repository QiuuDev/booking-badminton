<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings'; // adjust if your table name differs
    protected $fillable = ['user_id', 'court_id', 'start_time', 'end_time', 'status'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];
}