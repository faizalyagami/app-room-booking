<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notifikasi Booking Ruangan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6; font-size:14px; color:#333;">

    <p>Assalamualaikum, <strong>{{ $receiver_name }}</strong></p>

    @if ($to_role == 'ADMIN')
        @if ($status == 'DIBUAT')
            <p>Ada <strong>request booking baru</strong> dengan data berikut:</p>
        @elseif ($status == 'BATAL')
            <p>Request booking berikut ini sekarang <strong>dibatalkan</strong>:</p>
        @elseif ($status == 'DISETUJUI')
            <p>Request booking berikut ini sekarang <strong>disetujui</strong>:</p>
        @elseif ($status == 'DITOLAK')
            <p>Request booking berikut ini sekarang <strong>ditolak</strong>:</p>
        @endif

    @elseif ($to_role == 'USER')
        @if ($status == 'DIBUAT')
            <p>Request kamu <strong>berhasil dibuat</strong>! Berikut ini datanya:</p>
        @elseif ($status == 'BATAL')
            <p>Request kamu sekarang <strong>dibatalkan</strong>! Berikut ini datanya:</p>
        @elseif ($status == 'DISETUJUI')
            <p>Selamat! Request kamu sudah <strong>disetujui</strong>! Berikut ini datanya:</p>
        @elseif ($status == 'DITOLAK')
            <p>Maaf, request kamu <strong>ditolak</strong>! Berikut ini datanya:</p>
        @endif
    @endif

    @php
        $statusLabel = match($status) {
            'DIBUAT'    => 'PENDING',
            'BATAL'     => 'DIBATALKAN',
            'DISETUJUI' => 'DISETUJUI',
            'DITOLAK'   => 'DITOLAK',
            default     => $status,
        };
    @endphp

    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; margin-top: 10px; width: 100%; max-width: 500px;">
        <tr style="background:#f4f4f4;">
            <td><strong>Pemohon</strong></td>
            <td>{{ $user_name }}</td>
        </tr>
        <tr>
            <td><strong>Nama Ruangan</strong></td>
            <td>{{ $room_name }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            <td><strong>Waktu Mulai</strong></td>
            <td>{{ $start_time }}</td>
        </tr>
        <tr>
            <td><strong>Waktu Selesai</strong></td>
            <td>{{ $end_time }}</td>
        </tr>
        <tr>
            <td><strong>Keperluan</strong></td>
            <td>{{ $purpose }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>{{ $statusLabel }}</td>
        </tr>
    </table>

    <p style="margin-top: 15px;">
        <a href="{{ $url }}" 
           style="display:inline-block;background:#3498db;color:#fff;
                  padding:10px 15px;border-radius:5px;text-decoration:none;">
            Lihat Detail Booking
        </a>
    </p>

    <p style="font-size: 12px; color: #777; margin-top: 30px;">
        Email ini dikirim otomatis oleh sistem Room Booking.<br>
        Mohon tidak membalas email ini.
    </p>

</body>
</html>
