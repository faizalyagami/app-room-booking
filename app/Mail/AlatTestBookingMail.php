<?php

namespace App\Mail;

use App\Models\AlatTestBooking;
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
    public $adminName;
    public $url;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $userName, 
        string $alatName, 
        string $date, 
        string $startTime, 
        string $endTime, 
        string $purpose, 
        string $role, 
        string $adminName, 
        string $url, 
        string $status
    )
    {
        $this->userName = $userName;
        $this->alatName = $alatName;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->purpose = $purpose;
        $this->role = $role;
        $this->adminName = $adminName;
        $this->url = $url;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Notifikasi Peminjaman Alat Test')
                    ->markdown('emails.alat_test_notification');
    }

}
