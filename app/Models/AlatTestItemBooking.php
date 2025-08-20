<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTestItemBooking extends Model
{
    use HasFactory;
    protected $table = 'booking_alat_items';

    protected $fillable = [
        'booking_alat_id',
        'alat_test_item_id'
    ];

    // Relasi ke booking alat test
    public function alatTestBooking()
    {
        return $this->belongsTo(AlatTestBooking::class, 'booking_alat_id', 'id');
    }

    // Relasi ke alat test item
    public function alatTestItem()
    {
        return $this->belongsTo(AlatTestItem::class);
    }
}
