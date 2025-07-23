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

    public function alatTest()
    {
        return $this->belongsTo(AlatTest::class, 'alat_test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
