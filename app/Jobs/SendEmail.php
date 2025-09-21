<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use App\Mail\BookingMail;
use App\Mail\AlatTestBookingMail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $receivers; // bisa 1 atau lebih email
    protected string $type;     // 'room' | 'alat_test'
    protected array $data;

    /**
     * @param string|array $receivers contoh: "mhs1@univ.ac.id" atau ["mhs1@univ.ac.id", "admin@univ.ac.id"]
     * @param string $type
     * @param array $data
     */
    public function __construct(string|array $receivers, string $type, array $data)
    {
        // pastikan selalu jadi array
        $this->receivers = is_array($receivers) ? $receivers : [$receivers];
        $this->type      = $type;
        $this->data      = $data;
    }

    public function handle(): void
    {
        foreach ($this->receivers as $receiver) {
            \Log::info("Sending {$this->type} email to {$receiver}");

            if ($this->type === 'room') {
                Mail::to($receiver)->send(new BookingMail(
                    $this->data['user_name'],
                    $this->data['room_name'],
                    $this->data['date'],
                    $this->data['start_time'],
                    $this->data['end_time'],
                    $this->data['purpose'],
                    $this->data['to_role'],
                    $this->data['receiver_name'],
                    $this->data['url'],
                    $this->data['status']
                ));
            } elseif ($this->type === 'alat_test') {
                Mail::to($receiver)->send(new AlatTestBookingMail($this->data));
            }
        }
    }
}
