<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingAlat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'alat_test_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'purpose',
        'status'
    ];

    public function bookingAlat()
    {
        return $this->hasOne(AlatTest::class, 'id', 'alat_test_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
