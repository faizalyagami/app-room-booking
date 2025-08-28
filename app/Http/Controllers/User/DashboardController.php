<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use DataTables;

use App\Models\BookingList;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function dashboard_booking_list(){
        $today = Carbon::today()->toDateString();

        $data = BookingList::where('user_id', Auth::user()->id)
        ->whereDate('created_at', '=', $today)
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
            ->whereDate('created_at', '=', $today)
            ->count();
        $booking_lifetime = BookingList::where([
                ['user_id', Auth::user()->id],
            ])->count();

        $booking_tool_today = BookingAlat::where('user_id', Auth::user()->id)
            ->whereDate('created_at', '=', $today)
            ->count();
        $booking_tool_lifetime = BookingAlat::where([
                ['user_id', Auth::user()->id],
            ])->count();

        if (Hash::check("mahasiswa", $user->password)) {
            return redirect()->route('user.change-pass.index');
        }

        return view('pages.user.dashboard', [
            'booking_today'     => $booking_today,
            'booking_lifetime'  => $booking_lifetime,
            'booking_tool_today'     => $booking_tool_today,
            'booking_tool_lifetime'  => $booking_tool_lifetime,
        ]);
    }
}
