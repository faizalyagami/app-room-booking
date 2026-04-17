<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'photo',
        'plot_image',
        'plot_description',
        'plot_valid_from',
        'plot_valid_until'
    ];

    protected $casts = [
        'plot_valid_from' => 'date',
        'plot_valid_until' => 'date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function booking_list()
    {
        return $this->belongsTo(BookingList::class, 'id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingList::class, 'room_id');
    }

    public function isPlotValid()
    {
        $today = now()->toDateString();

        if (!$this->plot_valid_from && !$this->plot_valid_until) {
            return !is_null($this->plot_image);
        }

        if ($this->plot_valid_from && $this->plot_valid_from > $today) {
            return false;
        }

        if ($this->plot_valid_until && $this->plot_valid_until < $today) {
            return false;
        }

        return !is_null($this->plot_image);
    }
}
