<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatTestInout extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alat_test_in_outs';

    public function alatTestInoutItems ()
    {
        return $this->hasMany(AlatTestInoutItem::class, 'alat_test_in_out_id');
    }
}
