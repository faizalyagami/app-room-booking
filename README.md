## ROOMING (Room Booking)

Aplikasi booking ruangan sederhana

Pertama user membuat form, form otomatis akan berstatus PENDING. Lalu admin bisa menyetujui atau menolak permintaan booking user. Admin juga bisa membatalkan penyetujuan maupun penolakan booking.

Form booking dibuat untuk booking 1 hari.

Ada 7 status booking, di antaranya PENDING, DISETUJUI, DIGUNAKAN, DITOLAK, BATAL, SELESAI, dan EXPIRED.

### Penjelasan Status Booking

Berikut ini ialah penjelasan status booking yang dibuat oleh User. Otomatisasi perubahan status booking dilakukan dengan Laravel Scheduler.

1. PENDING. Ketika User baru mengajukan permintaan booking.
2. DISETUJUI. Ketika Admin menyetujui permintaan booking User. Aksi ini bisa dibatalkan dengan mengklik Batalkan di data booking User
3. DIGUNAKAN. Ketika User tengah menggunakan ruangan, dilihat berdasarkan tanggal, waktu mulai dan waktu selesai booking User.
4. DITOLAK. Ketika Admin menolak permintaan booking User. Aksi ini bisa dibatalkan dengan mengklik Setujui di data booking User.
5. BATAL. Ketika User membatalkan permintaan booking. Aksi ini tidak bisa dibatalkan
6. SELESAI. Ketika waktu booking selesai, dilihat berdasarkan tanggal, waktu mulai dan waktu selesai booking User.
7. EXPIRED. Ketika permintaan booking User dibiarkan PENDING sampai melewati waktu mulai booking.

Note: ROOMING menggunakan waktu Jakarta / Waktu Indonesia Barat. Jika ingin mengganti waktu yang digunakan, ganti nilai `APP_TIMEZONE` di .env

### Tech Stack

- Laravel 8
- Bootstrap 4
  lain-lain:
- Yajra Datatables
- Stisla Admin Theme

### Instalasi

- Clone atau Download
- Masuk ke folder ROOMING ini
- Copy .env.example ke .env (Jika tidak ada .env silakan buat di root folder)
- Sesuaikan konfigurasi .env seperti username dan password database dengan milikmu
- Jalankan `php artisan key:generate` untuk generate `APP_KEY` di .env
- Buat database MySQL dengan nama `db_rooming`
- Jalankan di terminal `composer install`
- Jalankan di terminal `php artisan migrate --seed`
- Buat Cron Job (Linux) atau Task Scheduler (Windows) untuk menjalankan perintah schedule Laravel karena ROOMING menggunakan [Laravel Scheduler](https://laravel.com/docs/8.x/scheduling).

### Menjalankan Laravel Scheduler di Linux (Ubuntu)

- Ketik `crontab -e` atau `sudo crontab -e` di terminal
- Sistem akan membuatkan sebuah file jika ini adalah kali pertama
- Masukkan perintah `* * * * * cd /path/ke/projekmu && php artisan schedule:run >> /dev/null 2>&1`
- Ganti `/path/ke/projekmu` dengan path projek ROOMING kamu
- Tekan `Ctrl+x` lalu tekan `y` dan enter
- Untuk melihat cronjob akun User yang saat ini dipakai ketik `crontab -l` atau `sudo crontab -l`

### Jalankan Aplikasi

```
php artisan serve
```

### Jalankan Queues

[Queues](https://laravel.com/docs/8.x/queues) digunakan untuk pengiriman email notifikasi pembuatan, pembatalan, penyetujuan, dan penolakan request booking.

```
php artisan queue:work
```

### User

User\
Username: user\
Password: user

Admin\
Username: admin\
Password: admin

### Lisensi

ROOMING menggunakan [lisensi MIT](https://github.com/fajarwz/rooming/blob/main/LICENSE)

### Demo

[Youtube](https://youtu.be/ZZL4VrJCA3E)

### Misc

Aplikasi ini memanfaatkan Blade Component dengan teknik reusable component. Form input hanya 1, tapi dipanggil di setiap fitur tambah data. Input field juga ada 1 tapi dipanggil berkali-kali di setiap fitur tambah data. Lumayan untuk belajar blade component.

by: fajarwz

[Visit my Web](https://fajarwz.netlify.app)\
[Visit my Medium](https://fajarwz.medium.com)\
[Get Connected](https://linkedin.com/in/fajarwz)\

<?php

namespace Database\Seeders;

use App\Models\BookingList;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlottedRoomSeeder extends Seeder
{
    public function run()
    {
        $labUserId = 2; // petugas laboratorium
        $startDate = Carbon::create(2025, 9, 22); // 22 September 2025
        $endDate = Carbon::create(2026, 1, 9);    // 9 Januari 2026

        $schedule = [
                '1' => [ // SENIN
                    'Seminar 1 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 15 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 16 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 17 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 18 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 19 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 20 Lantai 4' => [
                        ['07:20:00','10:10:00']
                    ],
                ],
                '2' => [ // SELASA
                    'Seminar 1 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Lab Psikologi 15 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 16 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 17 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 18 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 19 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 20 Lantai 4' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Lab Psikologi 21 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 22 Lantai 4' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Ruang Tes Anak' => [
                        ['07:20:00','10:10:00']
                    ],
                ],
                '3' => [ // RABU
                    'Seminar 1 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 15 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 16 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 17 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 18 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 19 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 20 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 21 Lantai 4' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Ruang Tes Anak' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                ],
                '4' => [ // KAMIS
                    'Seminar 1 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Lab Psikologi 21 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Ruang Tes Anak' => [
                        ['12:20:00','15:10:00']
                    ],
                ],
                '5' => [ // JUMAT
                    'Seminar 1 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['13:10:00','16:00:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['13:10:00','16:00:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['13:10:00','16:00:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['13:10:00','16:00:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00']
                    ],
                    'Ruang Tes Anak' => [
                        ['07:20:00','10:10:00']
                    ],
                ],
                '6' => [ // SABTU
                    'Seminar 1 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 2 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 3 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 4 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 5 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 6 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 7 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 8 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 9 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 10 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 11 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 12 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Seminar 13 Lantai 3' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 15 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 16 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 17 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 18 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 19 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 20 Lantai 4' => [
                        ['07:20:00','10:10:00'],
                        ['12:20:00','15:10:00']
                    ],
                    'Lab Psikologi 21 Lantai 4' => [
                        ['07:20:00','10:10:00']
                    ],
                ],
            ];

        $count = 0;
        $rooms = Room::pluck('id', 'name')->toArray();

        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            $day = $date->dayOfWeekIso;
            if (isset($schedule[$day])) {
                foreach ($schedule[$day] as $roomName => $slots) {
                    if (!isset($rooms[$roomName])) continue;

                    foreach ($slots as [$start, $end]) {
                        $exists = BookingList::where('room_id', $rooms[$roomName])
                            ->where('date', $date->toDateString())
                            ->where('start_time', $start)
                            ->exists();

                        if (!$exists) {
                            BookingList::create([
                                'room_id' => $rooms[$roomName],
                                'user_id' => $labUserId,
                                'date' => $date->toDateString(),
                                'start_time' => $start,
                                'end_time' => $end,
                                'status' => 'BOOKING_BY_LAB',
                                'purpose' => 'Jadwal tetap laboratorium (plot semester ganjil 25/26)',
                                'is_fixed' => true,
                            ]);
                            $count++;
                        }
                    }
                }
            }
            $date->addDay();
        }

        $this->command->info("✅ {$count} jadwal plot laboratorium semester ganjil 2025/2026 berhasil ditambahkan!");
    }
}
