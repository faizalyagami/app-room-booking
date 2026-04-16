<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingAlat;
use App\Models\Room;
use App\Models\InfoImage; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use DataTables;

use App\Models\BookingList;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function dashboard_booking_list()
    {
        $today = Carbon::today()->toDateString();

        $data = BookingList::where('user_id', Auth::user()->id)
            ->whereDate('date', '=', $today)
            ->with([
                'room'
            ])->take(3);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $booking_today = BookingList::where('user_id', Auth::user()->id)
            ->whereDate('date', '=', $today)
            ->count();
        $booking_lifetime = BookingList::where([
            ['user_id', Auth::user()->id],
        ])->count();

        $booking_tool_today = BookingAlat::where('user_id', Auth::user()->id)
            ->whereDate('date', '=', $today)
            ->count();
        $booking_tool_lifetime = BookingAlat::where([
            ['user_id', Auth::user()->id],
        ])->count();

        // Ambil info gambar yang aktif (TANPA PILIH RUANGAN)
        $infoImages = InfoImage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($item) {
                return $item->isValid();
            });

        // Ambil ruangan yang memiliki plot aktif (opsional, jika masih ingin dipertahankan)
        $roomsWithPlot = Room::whereNotNull('plot_image')
            ->get()
            ->filter(function ($room) {
                return $room->isPlotValid();
            });

        if (Hash::check("mahasiswa", $user->password)) {
            return redirect()->route('user.change-pass.index');
        }

        return view('pages.user.dashboard', [
            'booking_today'     => $booking_today,
            'booking_lifetime'  => $booking_lifetime,
            'booking_tool_today'     => $booking_tool_today,
            'booking_tool_lifetime'  => $booking_tool_lifetime,
            'infoImages' => $infoImages, // Tambahkan ini
            'roomsWithPlot' => $roomsWithPlot, // Opsional
        ]);
    }
}
