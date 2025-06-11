<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTestItem extends Model
{
    use HasFactory;

    protected $fillable = ['alat_test_id', 'serial_number', 'status'];

    public function alatTest()
    {
        return $this->belongsTo(AlatTest::class);
    }
}
