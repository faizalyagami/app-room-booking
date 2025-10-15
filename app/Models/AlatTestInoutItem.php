<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatTestInoutItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alat_test_in_out_items';

    public function alatTestItem ()
    {
        return $this->belongsTo(AlatTestItem::class);
    }

    public function alatTestInout ()
    {
        return $this->belongsTo(AlatTestInoutItem::class, 'alat_test_in_out_id');
    }
}
