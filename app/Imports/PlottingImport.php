<?php
// app/Imports/PlottingImport.php

namespace App\Imports;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\Plotting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PlottingImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected $plotting;
    protected $labUserId;
    protected $rooms;
    protected $dayMap;
    protected $rowCount = 0;
    protected $plottingData = [];

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

    public function collection(Collection $rows)
    {
        // Hapus data existing untuk periode ini
        BookingList::where('status', 'BOOKING_BY_LAB')
            ->whereBetween('date', [$this->plotting->tanggal_mulai, $this->plotting->tanggal_selesai])
            ->delete();

        $startDate = Carbon::parse($this->plotting->tanggal_mulai);
        $endDate = Carbon::parse($this->plotting->tanggal_selesai);

        foreach ($rows as $row) {
            // Validasi data
            $hari = strtolower(trim($row['hari']));
            $roomName = trim($row['nama_ruangan']);
            $jamMulai = $this->formatTime($row['jam_mulai']);
            $jamSelesai = $this->formatTime($row['jam_selesai']);

            if (!isset($this->dayMap[$hari])) {
                continue;
            }

            if (!isset($this->rooms[$roomName])) {
                continue;
            }

            $dayNumber = $this->dayMap[$hari];
            $roomId = $this->rooms[$roomName];

            // Loop tanggal
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeekIso == $dayNumber) {
                    // Cek duplikat
                    $exists = BookingList::where('room_id', $roomId)
                        ->where('date', $currentDate->toDateString())
                        ->where('start_time', $jamMulai)
                        ->exists();

                    if (!$exists) {
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

                        $this->rowCount++;

                        // Simpan untuk data_plotting JSON
                        $this->plottingData[] = [
                            'hari' => $hari,
                            'room_name' => $roomName,
                            'start_time' => $jamMulai,
                            'end_time' => $jamSelesai,
                        ];
                    }
                }
                $currentDate->addDay();
            }
        }
    }

    private function formatTime($time)
    {
        // Handle format jam dari Excel (misal: 07:20 atau 7.20)
        if (is_numeric($time)) {
            // Excel serial time
            return Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($time))->format('H:i:s');
        }
        
        // String format
        $time = str_replace('.', ':', $time);
        if (strlen($time) == 5) {
            $time .= ':00';
        }
        return $time;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getPlottingData()
    {
        return $this->plottingData;
    }
}