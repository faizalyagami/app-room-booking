<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'description',
        'valid_from',
        'valid_until',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean'
    ];

    // Cek apakah masih berlaku
    public function isValid()
    {
        $today = now()->toDateString();

        if (!$this->valid_from && !$this->valid_until) {
            return $this->is_active;
        }

        if ($this->valid_from && $this->valid_from > $today) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $today) {
            return false;
        }

        return $this->is_active;
    }
}
