<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\BookingList;

class BookingCreatedNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(BookingList $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Booking Berhasil Dibuat',
            'room' => $this->booking->room->name,
            'date' => $this->booking->date,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'status' => 'PENDING',
            'message' => 'Booking ruangan ' . $this->booking->room->name . ' berhasil dibuat dan sedang menunggu persetujuan admin.',
            'booking_id' => $this->booking->id,
        ];
    }
}
