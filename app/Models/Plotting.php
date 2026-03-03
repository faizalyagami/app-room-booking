<?php
// app/Models/Plotting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plotting extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'data_plotting', // JSON untuk menyimpan konfigurasi plotting
        'created_by'
    ];

    protected $casts = [
        'data_plotting' => 'array',
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}