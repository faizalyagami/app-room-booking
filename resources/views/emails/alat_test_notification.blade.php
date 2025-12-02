@component('mail::message')
# Notifikasi Peminjaman Alat Test

Assalamualaikum **{{ $userName }}**,

Status peminjaman alat test Anda saat ini: **{{ $status }}**

**Detail Peminjaman:**
- Alat Test : {{ $alatName }}
- Tanggal   : {{ $date }}
- Waktu     : {{ $startTime }} - {{ $endTime }}
- Keperluan : {{ $purpose }}

@component('mail::button', ['url' => $url])
Lihat Detail
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent
