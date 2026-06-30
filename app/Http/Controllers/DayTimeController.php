<?php

namespace App\Http\Controllers;

use App\Models\BookingList;
use App\Models\DayTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DayTimeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get available times for a specific room and date
     */
    public function getTimes(Request $request)
    {
        // Validasi input
        $request->validate([
            'date' => 'required|date',
            'room' => 'required|integer'
        ]);

        $date = $request->date;
        $roomId = $request->room;

        // Konversi hari (0=Minggu, 1=Senin, dst)
        $day = date('w', strtotime($date));

        // Ambil waktu yang tersedia untuk hari tersebut
        $times = DayTime::where('day', $day)
            ->where('status', 'AKTIF')
            ->orderBy('start_time')
            ->get();

        // Ambil booking yang sudah ada untuk tanggal dan ruangan ini
        // Termasuk DISETUJUI dan BOOKING_BY_LAB
        $bookings = BookingList::where('date', $date)
            ->where('room_id', $roomId)
            ->whereIn('status', ['DISETUJUI', 'BOOKING_BY_LAB'])
            ->get(['start_time', 'status']);

        // Format bookings untuk frontend
        $formattedBookings = [];
        foreach ($bookings as $booking) {
            $formattedBookings[] = [
                'start_time' => $booking->start_time,
                'status' => $booking->status
            ];
        }

        if (count($times)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Waktu ditemukan!',
                'data' => [
                    'times' => $times,
                    'bookings' => $formattedBookings
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Waktu tidak ditemukan.',
            'data' => [
                'times' => [],
                'bookings' => []
            ]
        ], 200);
    }

    /**
     * Check overlap untuk custom waktu booking
     */
    public function checkOverlap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'room' => 'required|integer|exists:rooms,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'overlap' => false,
                'message' => 'Validasi gagal: ' . $validator->errors()->first()
            ], 400);
        }

        $date = $request->date;
        $roomId = $request->room;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Cek apakah waktu mulai sudah lewat
        $now = Carbon::now();
        $bookingDateTimeStart = Carbon::parse($date . ' ' . $startTime);

        if ($bookingDateTimeStart->lessThanOrEqualTo($now)) {
            return response()->json([
                'overlap' => true,
                'message' => 'Waktu yang dipilih sudah lewat. Silakan pilih waktu yang akan datang.'
            ]);
        }

        // Cek overlap dengan booking yang sudah ada
        $overlap = BookingList::where('date', $date)
            ->where('room_id', $roomId)
            ->whereIn('status', ['DISETUJUI', 'BOOKING_BY_LAB'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'overlap' => true,
                'message' => 'Maaf, ruangan sudah dibooking pada waktu tersebut.'
            ]);
        }

        return response()->json([
            'overlap' => false,
            'message' => 'Waktu tersedia. Silakan lanjutkan booking.'
        ]);
    }
}
