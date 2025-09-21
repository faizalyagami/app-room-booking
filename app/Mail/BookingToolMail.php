<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingToolMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $user_name;
    public string $tool_name;
    public string $date;
    public string $start_time;
    public string $end_time;
    public string $purpose;
    public string $to_role;
    public string $receiver_name;
    public string $url;
    public string $status;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $user_name,
        string $tool_name,
        string $date,
        string $start_time,
        string $end_time,
        string $purpose,
        string $to_role,
        string $receiver_name,
        string $url,
        string $status
    ) {
        $this->user_name     = $user_name;
        $this->tool_name     = $tool_name;
        $this->date          = $date;
        $this->start_time    = $start_time;
        $this->end_time      = $end_time;
        $this->purpose       = $purpose;
        $this->to_role       = $to_role;
        $this->receiver_name = $receiver_name;
        $this->url           = $url;
        $this->status        = $status;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = "Peminjaman Alat - Status: {$this->status}";

        return $this->subject($subject)
                    ->markdown('emails.booking_tool');
    }
}
