@component('mail::message')
# Hai {{ $user_name }},

Booking ruangan Anda sudah **DISETUJUI** 🎉

Berikut detailnya:

| Nama Ruangan | {{ $room_name }} |
|--------------|------------------|
| Tanggal      | {{ $date }} |
| Waktu Mulai  | {{ $start_time }} |
| Waktu Selesai| {{ $end_time }} |
| Keperluan    | {{ $purpose }} |

@component('mail::button', ['url' => $url])
Lihat Booking
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
