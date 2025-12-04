<?php

namespace App\Mail;

use App\Models\AlatTestBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlatTestBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name; 
    public $items;   
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
        string $user_name,
        array $items,
        string $date,
        string $start_time,
        string $end_time,
        string $purpose,
        string $to_role,     
        string $receiver_name,
        string $url, 
        string $status
    )
    {
        $this->user_name = $user_name;
        $this->items = $items;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->purpose = $purpose;
        $this->to_role = $to_role;           
        $this->receiver_name = $receiver_name; 
        $this->url = $url;
        $this->status = $status;
    }

    public function build()
    {   
        $subject = 'Notifikasi Peminjaman Alat Test';

        // Custom subject berdasarkan role dan status
        if ($this->to_role === 'ADMIN') {
            if ($this->status === 'PENDING') {
                $subject = 'Request peminjaman alat test baru';
            } elseif ($this->status === 'BATAL') {
                $subject = 'Request peminjaman alat test dibatalkan';
            } elseif ($this->status === 'DISETUJUI') {
                $subject = 'Request peminjaman alat test disetujui';
            } elseif ($this->status === 'DITOLAK') {
                $subject = 'Request peminjaman alat test ditolak';
            } elseif ($this->status === 'DIKEMBALIKAN') {
                $subject = 'Alat test telah dikembalikan';
            }
        } elseif ($this->to_role === 'USER') {
            if ($this->status === 'PENDING') {
                $subject = 'Request peminjaman alat test berhasil dibuat';
            } elseif ($this->status === 'BATAL') {
                $subject = 'Request peminjaman alat test berhasil dibatalkan';
            } elseif ($this->status === 'DISETUJUI') {
                $subject = 'Selamat! Request peminjaman alat test kamu disetujui';
            } elseif ($this->status === 'DITOLAK') {
                $subject = 'Maaf, request peminjaman alat test kamu ditolak';
            } elseif ($this->status === 'DIKEMBALIKAN') {
                $subject = 'Alat test telah dikembalikan';
            }
        }

        return $this->subject($subject)
                    ->view('emails.booking-tool')
                    ->with([
                    'userName'   => $this->user_name,
                    'items'      => $this->items,
                    'date'       => $this->date,
                    'startTime'  => $this->start_time,
                    'endTime'    => $this->end_time,
                    'purpose'    => $this->purpose,
                    'role'       => $this->to_role,
                    'receiverName'=> $this->receiver_name,
                    'url'        => $this->url,
                    'status'     => $this->status,
                ]);
    }

}
