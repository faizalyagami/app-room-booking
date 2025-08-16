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
    
    public function getTimes(Request $request)
    {
        $day = date('w', strtotime($request->date));
        $times = DayTime::where('day', $day)->whereStatus('AKTIF')->get();

        $bookings = BookingList::where('date', $request->date)
            ->where('room_id', $request->room)
            ->where('status', 'DISETUJUI')
            ->pluck("start_time");

        if(count($times)) {
            return response()->json(['status' => 'success', 'message' => 'waktu ditemukan!.', 'data' => ['times' => $times, 'bookings' => $bookings]], 200);
        }

        return response()->json(['status' =>'error', 'message' => 'waktu tidak ditemukan.', 'data' => null], 201);
    }
}
