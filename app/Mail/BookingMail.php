<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name;
    public $room_name;
    public $date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $to_role;
    public $receiver_name;
    public $url;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $user_name,
        $room_name,
        $date,
        $start_time,
        $end_time,
        $purpose,
        $to_role,
        $receiver_name,
        $url,
        $status
    ) {
        $this->user_name      = $user_name;
        $this->room_name      = $room_name;
        $this->date           = $date;
        $this->start_time     = $start_time;
        $this->end_time       = $end_time;
        $this->purpose        = $purpose;
        $this->to_role        = $to_role;
        $this->receiver_name  = $receiver_name;
        $this->url            = $url;
        $this->status         = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Notifikasi Booking';

        if ($this->to_role === 'ADMIN') {
            if ($this->status === 'DIBUAT') {
                $subject = 'Request booking baru';
            } elseif ($this->status === 'BATAL') {
                $subject = 'Request booking dibatalkan';
            } elseif ($this->status === 'DISETUJUI') {
                $subject = 'Request booking berhasil disetujui';
            } elseif ($this->status === 'DITOLAK') {
                $subject = 'Request booking berhasil ditolak';
            }
        } elseif ($this->to_role === 'USER') {
            if ($this->status === 'DIBUAT') {
                $subject = 'Request booking berhasil dibuat';
            } elseif ($this->status === 'BATAL') {
                $subject = 'Request booking berhasil dibatalkan';
            } elseif ($this->status === 'DISETUJUI') {
                $subject = 'Selamat! Request booking kamu sudah disetujui';
            } elseif ($this->status === 'DITOLAK') {
                $subject = 'Maaf, request booking kamu ditolak';
            }
        }

        return $this->subject($subject)
            ->view('emails.booking')
            ->with([
                'receiver_name' => $this->receiver_name,
                'user_name'     => $this->user_name,
                'room_name'     => $this->room_name,
                'date'          => $this->date,
                'start_time'    => $this->start_time,
                'end_time'      => $this->end_time,
                'purpose'       => $this->purpose,
                'status'        => $this->status,
                'url'           => $this->url,
            ]);
    }
}
