<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTestBooking extends Model
{
    use HasFactory;
    protected $table = 'booking_alats';

    protected $fillable = [
        'alat_test_id',
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

    // Relasi ke Alat Test
    public function alatTest()
    {
        return $this->belongsTo(AlatTest::class);
    }
}
