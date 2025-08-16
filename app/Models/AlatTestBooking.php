<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTestBooking extends Model
{
    use HasFactory;
    protected $table = 'booking_alats';

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'purpose',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Alat Test Item
    public function alatTestItemBooking()
    {
        return $this->hasMany(AlatTestItemBooking::class, 'booking_alat_id', 'id');
    }
}
