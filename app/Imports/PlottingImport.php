<?php
// app/Imports/PlottingImport.php

namespace App\Imports;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\Plotting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class PlottingImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $plotting;
    protected $labUserId;
    protected $rooms;
    protected $dayMap;
    protected $rowCount = 0;
    protected $plottingData = [];
    protected $errors = [];

    public function __construct($plotting, $labUserId)
    {
        $this->plotting = $plotting;
        $this->labUserId = $labUserId;
        $this->rooms = Room::pluck('id', 'name')->toArray();
        
        // Map hari ke number (ISO-8601)
        $this->dayMap = [
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6,
            'minggu' => 7,
        ];
    }

    // app/Imports/PlottingImport.php

    public function collection(Collection $rows)
    {
        // Validasi apakah ada data
        if ($rows->isEmpty()) {
            $this->errors[] = 'File Excel kosong';
            return;
        }

        // Hapus data existing untuk periode ini
        BookingList::where('status', 'BOOKING_BY_LAB')
            ->whereBetween('date', [$this->plotting->tanggal_mulai, $this->plotting->tanggal_selesai])
            ->delete();

        $startDate = Carbon::parse($this->plotting->tanggal_mulai);
        $endDate = Carbon::parse($this->plotting->tanggal_selesai);
        
        \Log::info('Start Import', [
            'periode' => $startDate->format('Y-m-d') . ' s/d ' . $endDate->format('Y-m-d'),
            'total_rows' => $rows->count()
        ]);
        
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            
            // Validasi kolom required
            if (!isset($row['hari']) || empty(trim($row['hari']))) {
                $this->errors[] = "Baris {$rowNumber}: Kolom HARI tidak boleh kosong";
                continue;
            }
            
            if (!isset($row['nama_ruangan']) || empty(trim($row['nama_ruangan']))) {
                $this->errors[] = "Baris {$rowNumber}: Kolom NAMA RUANGAN tidak boleh kosong";
                continue;
            }
            
            if (!isset($row['jam_mulai']) || empty($row['jam_mulai'])) {
                $this->errors[] = "Baris {$rowNumber}: Kolom JAM MULAI tidak boleh kosong";
                continue;
            }
            
            if (!isset($row['jam_selesai']) || empty($row['jam_selesai'])) {
                $this->errors[] = "Baris {$rowNumber}: Kolom JAM SELESAI tidak boleh kosong";
                continue;
            }

            // Validasi hari
            $hari = strtolower(trim($row['hari']));
            if (!isset($this->dayMap[$hari])) {
                $this->errors[] = "Baris {$rowNumber}: Hari '{$row['hari']}' tidak valid. Gunakan: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu";
                continue;
            }

            // Validasi ruangan
            $roomName = trim($row['nama_ruangan']);
            if (!isset($this->rooms[$roomName])) {
                $this->errors[] = "Baris {$rowNumber}: Ruangan '{$roomName}' tidak ditemukan di database";
                // Log daftar ruangan yang tersedia untuk debugging
                \Log::warning('Ruangan tidak ditemukan', [
                    'dicari' => $roomName,
                    'tersedia' => array_keys($this->rooms)
                ]);
                continue;
            }

            // Format jam
            try {
                $jamMulai = $this->formatTime($row['jam_mulai']);
                $jamSelesai = $this->formatTime($row['jam_selesai']);
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: Format jam tidak valid. Gunakan format HH:MM (contoh: 07:20)";
                continue;
            }

            // Validasi jam mulai < jam selesai
            if ($jamMulai >= $jamSelesai) {
                $this->errors[] = "Baris {$rowNumber}: Jam mulai harus lebih kecil dari jam selesai";
                continue;
            }

            $dayNumber = $this->dayMap[$hari];
            $roomId = $this->rooms[$roomName];

            
            $currentDate = $startDate->copy();
            $insertCount = 0;
            
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeekIso == $dayNumber) {
                    
                    BookingList::create([
                        'room_id' => $roomId,
                        'user_id' => $this->labUserId,
                        'date' => $currentDate->toDateString(),
                        'start_time' => $jamMulai,
                        'end_time' => $jamSelesai,
                        'status' => 'BOOKING_BY_LAB',
                        'purpose' => 'Jadwal tetap laboratorium (plot ' . $this->plotting->semester . ' ' . $this->plotting->tahun_ajaran . ')',
                        'is_fixed' => true,
                    ]);

                    $insertCount++;
                    $this->rowCount++;

                    // Simpan untuk data_plotting JSON
                    $key = $hari . '|' . $roomName . '|' . $jamMulai . '|' . $jamSelesai;
                    if (!isset($this->plottingData[$key])) {
                        $this->plottingData[$key] = [
                            'hari' => $hari,
                            'room_name' => $roomName,
                            'start_time' => $jamMulai,
                            'end_time' => $jamSelesai,
                        ];
                    }
                }
                $currentDate->addDay();
            }
            
            // if ($insertCount == 0) {
            //     $this->errors[] = "Baris {$rowNumber}: Tidak ada jadwal yang ditambahkan. Mungkin sudah ada duplikat?";
            // } else {
                \Log::info("Baris {$rowNumber}: Berhasil menambahkan {$insertCount} jadwal");
            // }
        }
    }

    private function formatTime($time)
    {
        // Handle format jam dari Excel
        if (is_numeric($time)) {
            // Excel serial time (misal: 0.305555 = 07:20)
            $totalSeconds = intval($time * 24 * 60 * 60);
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        // String format
        $time = str_replace('.', ':', trim($time));
        
        // Handle format"8:00" menjadi "08:00:00"
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            return sprintf('%02d:%02d:00', $matches[1], $matches[2]);
        }
        
        // Handle format"07:20"
        if (preg_match('/^(\d{2}):(\d{2})$/', $time, $matches)) {
            return $time . ':00';
        }
        
        // Handle format lengkap "07:20:00"
        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $time)) {
            return $time;
        }
        
        throw new \Exception("Format jam tidak dikenal: {$time}");
    }

    
    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getPlottingData()
    {
        return array_values($this->plottingData);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}