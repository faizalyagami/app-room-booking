<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatTest extends Model
{
    use HasFactory;

    protected $table = 'alat_tests';

    protected $fillable = ['name', 'description', 'photo'];

    public function items()
    {
        return $this->hasMany(AlatTestItem::class);
    }
}
