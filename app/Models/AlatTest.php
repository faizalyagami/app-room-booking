<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alat_tests';

    protected $fillable = ['name', 'description', 'photo'];

    protected $casts = [
        'name' => 'string',
        'description' => 'string'
    ];

    public function items()
    {
        return $this->hasMany(AlatTestItem::class);
    }

    //Total stok (jumlah semua unit)
    public function getStockAttribute()
    {
        return $this->items()->count();
    }

    //Stok tersedia
    public function getAvaliableStockAttribute()
    {
        return $this->items()->where('status', 'tersedia')->count();
    }

    public function getBorrowedStockAttribute()
    {
        return $this->items()->where('status', 'dipinjam')->count();
    }
}
