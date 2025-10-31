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

        // Mapping jadwal resmi (dari tabel warna)
        $schedule = [
            '1' => [ // SENIN
                // Seminar 1–13 Lantai 3 jam 07.20-10.10 & 12.20-15.10
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 3 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 4 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 5 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 6 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 7 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 8 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 9 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 10 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 11 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 12 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 13 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 14 Lantai 3' => [['12:20:00','15:10:00']],
                'Lab Psikologi 15 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 16 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 17 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 18 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 19 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 20 Lantai 4' => [['07:20:00','10:10:00']],
            ],
            '2' => [ // SELASA
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 3 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 4 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 5 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 7 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 8 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 9 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 10 Lantai 3' => [['12:20:00','15:10:00']],
                'Lab Psikologi 16 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 17 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 18 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 19 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 20 Lantai 4' => [['12:20:00','15:10:00']],
                'Ruang Tes Anak' => [['07:20:00','10:10:00']],
            ],
            '3' => [ // RABU
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 3 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 4 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 5 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 6 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 7 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 8 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 9 Lantai 3' => [['12:20:00','15:10:00']],
                'Lab Psikologi 19 Lantai 4' => [['12:20:00','15:10:00']],
                'Ruang Tes Anak' => [['07:20:00','10:10:00']],
                'Ruang Tes Anak' => [['12:20:00','15:10:00']],
            ],
            '4' => [ // KAMIS
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 3 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 4 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 5 Lantai 3' => [['12:20:00','15:10:00']],
                'Lab Psikologi 20 Lantai 4' => [['12:20:00','15:10:00']],
            ],
            '5' => [ // JUMAT
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['13:10:00','16:00:00']],
                'Seminar 3 Lantai 3' => [['13:10:00','16:00:00']],
                'Seminar 7 Lantai 3' => [['13:10:00','16:00:00']],
                'Seminar 8 Lantai 3' => [['13:10:00','16:00:00']],
                'Seminar 9 Lantai 3' => [['07:20:00','10:10:00']],
                'Lab Psikologi 15 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 16 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 17 Lantai 4' => [['13:10:00','16:00:00']],
            ],
            '6' => [ // SABTU
                'Seminar 1 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 2 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 3 Lantai 3' => [['07:20:00','10:10:00']],
                'Seminar 4 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 5 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 6 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 7 Lantai 3' => [['12:20:00','15:10:00']],
                'Seminar 8 Lantai 3' => [['12:20:00','15:10:00']],
                'Lab Psikologi 15 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 16 Lantai 4' => [['07:20:00','10:10:00']],
                'Lab Psikologi 17 Lantai 4' => [['12:20:00','15:10:00']],
                'Lab Psikologi 18 Lantai 4' => [['12:20:00','15:10:00']],
                'Lab Psikologi 19 Lantai 4' => [['12:20:00','15:10:00']],
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
