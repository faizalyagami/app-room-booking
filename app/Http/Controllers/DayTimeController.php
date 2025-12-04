<?php

namespace App\Http\Controllers;

use App\Models\BookingList;
use App\Models\DayTime;
use Illuminate\Http\Request;

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
            ->get(['start_time', 'status']); // Ambil juga status

        // Format bookings untuk frontend
        $formattedBookings = [];
        foreach ($bookings as $booking) {
            $formattedBookings[] = [
                'start_time' => $booking->start_time,
                'status' => $booking->status
            ];
        }

        if(count($times)) {
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
}