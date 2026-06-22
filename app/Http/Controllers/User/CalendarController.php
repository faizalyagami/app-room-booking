<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingList;
use App\Models\Room;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        $roomId = $request->get('room_id', null);

        $rooms = Room::orderBy('name')->get();

        // Ambil data booking untuk bulan tersebut
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $bookings = BookingList::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('status', ['DISETUJUI', 'DIGUNAKAN', 'BOOKING_BY_LAB', 'SELESAI'])
            ->when($roomId, function ($query) use ($roomId) {
                return $query->where('room_id', $roomId);
            })
            ->with(['room', 'user'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Kelompokkan booking per tanggal
        $bookingsByDate = $bookings->groupBy('date');

        // Data untuk kalender
        $calendarData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->toDateString();
            $calendarData[$dateKey] = [
                'date' => $currentDate->copy(),
                'isToday' => $currentDate->isToday(),
                'isWeekend' => $currentDate->isWeekend(),
                'bookings' => $bookingsByDate->get($dateKey, collect()),
            ];
            $currentDate->addDay();
        }

        // Data untuk dropdown filter bulan
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }

        // Data untuk dropdown filter tahun
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('pages.user.calendar.index', compact(
            'calendarData',
            'rooms',
            'bookings',
            'year',
            'month',
            'roomId',
            'months',
            'years',
            'startDate',
            'endDate'
        ));
    }

    public function getBookings(Request $request)
    {
        $date = $request->get('date');
        $roomId = $request->get('room_id');

        $query = BookingList::where('date', $date)
            ->whereIn('status', ['DISETUJUI', 'DIGUNAKAN', 'BOOKING_BY_LAB', 'SELESAI'])
            ->with(['room', 'user']);

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        $bookings = $query->orderBy('start_time')->get();

        return response()->json($bookings);
    }
    public function getBookingDetail($id)
    {
        $booking = BookingList::with(['room', 'user'])->findOrFail($id);
        return response()->json($booking);
    }
}
