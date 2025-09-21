<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name;
    public $room_name;
    public $date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $url;

    public function __construct($user_name, $room_name, $date, $start_time, $end_time, $purpose, $url)
    {
        $this->user_name = $user_name;
        $this->room_name = $room_name;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->purpose = $purpose;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Booking Ruangan Anda Disetujui")
                    ->markdown('emails.booking.approved');
    }
}
