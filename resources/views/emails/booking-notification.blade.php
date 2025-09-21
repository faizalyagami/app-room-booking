<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Booking Ruangan</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #007bff; color: #ffffff; border-radius: 8px 8px 0 0;">
                <h2 style="margin: 0;">Notifikasi Booking Ruangan</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Halo <strong>{{ $receiverName }}</strong>,</p>
                <p>
                    Booking ruangan oleh <strong>{{ $userName }}</strong> telah
                    <strong style="color: {{ $status == 'DISETUJUI' ? 'green' : 'red' }}">
                        {{ $status }}
                    </strong>.
                </p>

                <h4>Detail Booking:</h4>
                <ul>
                    <li><strong>Ruangan:</strong> {{ $roomName }}</li>
                    <li><strong>Tanggal:</strong> {{ $date }}</li>
                    <li><strong>Waktu:</strong> {{ $startTime }} - {{ $endTime }}</li>
                    <li><strong>Keperluan:</strong> {{ $purpose }}</li>
                </ul>

                <p>
                    Silakan cek detail selengkapnya melalui link berikut:<br>
                    <a href="{{ $url }}" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;">
                        Lihat Detail Booking
                    </a>
                </p>

                <p style="margin-top: 20px; font-size: 12px; color: #888;">
                    Email ini dikirim otomatis oleh sistem Booking Ruangan. Mohon tidak membalas email ini.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
