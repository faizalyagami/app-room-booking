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

        // Tambahkan status PENDING agar semua booking muncul
        $bookings = BookingList::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('status', ['DISETUJUI', 'DIGUNAKAN', 'BOOKING_BY_LAB', 'SELESAI', 'PENDING'])
            ->when($roomId, function ($query) use ($roomId) {
                return $query->where('room_id', $roomId);
            })
            ->with(['room', 'user'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Kelompokkan booking per tanggal
        $bookingsByDate = $bookings->groupBy('date');

        // Set startOfWeek ke Senin (Carbon::MONDAY)
        $calendarData = [];
        $currentDate = $startDate->copy()->startOfWeek(Carbon::MONDAY);
        $endDatePlusWeek = $endDate->copy()->endOfWeek(Carbon::MONDAY);

        while ($currentDate <= $endDatePlusWeek) {
            $dateKey = $currentDate->toDateString();
            $calendarData[$dateKey] = [
                'date' => $currentDate->copy(),
                'isToday' => $currentDate->isToday(),
                'isCurrentMonth' => $currentDate->month == $month && $currentDate->year == $year,
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

    public function getBookingDetail($id)
    {
        $booking = BookingList::with(['room', 'user'])->findOrFail($id);
        return response()->json($booking);
    }
}
