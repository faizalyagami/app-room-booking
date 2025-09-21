<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlatTestBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $alatName;
    public $date;
    public $startTime;
    public $endTime;
    public $purpose;
    public $role;
    public $url;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(AlatTestBooking $booking, $subjectText = 'Notifikasi Peminjaman Alat Test')
    {
        $this->booking = $booking;
        $this->subjectText = $subjectText;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->markdown('emails.alat_test_booking');
    }

}
