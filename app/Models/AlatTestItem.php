<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTestItem extends Model
{
    use HasFactory;

    protected $fillable = ['alat_test_id', 'serial_number', 'status'];

    protected $casts = ['serial_number' => 'string', 'status' => 'string'];

    protected $attributes = ['status' => 'tersedia'];

    public function alatTest()
    {
        return $this->belongsTo(AlatTest::class);
    }

    public function alatTestItemBookings() 
    {
        return $this->hasMany(AlatTestItemBooking::class);
    }
}
