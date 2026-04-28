<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingList extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'room_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'is_fixed',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $startDateTime = Carbon::parse($model->date . ' ' . $model->start_time);
            $now = Carbon::now();
            
            // Jika waktu mulai sudah lewat, set status EXPIRED
            if ($startDateTime->lessThanOrEqualTo($now)) {
                $model->status = 'EXPIRED';
            }
        });
    }

    public function room(){
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
