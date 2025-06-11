<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTest;
use App\Models\BookingAlat;
use Illuminate\Http\Request;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $booking_list_all       = BookingList::all()->count();
        $booking_list_pending   = BookingList::where('status', 'PENDING')->count();
        $booking_list_disetujui = BookingList::where('status', 'DISETUJUI')->count();
        $booking_list_digunakan = BookingList::where('status', 'DIGUNAKAN')->count();
        $booking_list_selesai   = BookingList::where('status', 'SELESAI')->count();
        $booking_list_ditolak   = BookingList::where('status', 'DITOLAK')->count();
        $booking_list_batal     = BookingList::where('status', 'BATAL')->count();
        $booking_list_expired   = BookingList::where('status', 'EXPIRED')->count();

        $alat_test_all          = BookingAlat::count();
        $alat_test_pending      = BookingAlat::where('status', 'PENDING')->count();
        $alat_test_disetujui    = BookingAlat::where('status', 'DISETUJUI')->count();
        $alat_test_dikembalikan = BookingAlat::where('status', 'DIKEMBALIKAN')->count();
        $alat_test_ditolak      = BookingAlat::where('status', 'DITOLAK')->count();

        $room                   = Room::all()->count();
        $alat_test              = AlatTest::all()->count();
        $user                   = User::where('ROLE', 'USER')->count();

        return view('pages.admin.dashboard', [
            'booking_list_all'          => $booking_list_all,
            'booking_list_pending'      => $booking_list_pending,
            'booking_list_disetujui'    => $booking_list_disetujui,
            'booking_list_digunakan'    => $booking_list_digunakan,
            'booking_list_selesai'      => $booking_list_selesai,
            'booking_list_ditolak'      => $booking_list_ditolak,
            'booking_list_batal'        => $booking_list_batal,
            'booking_list_expired'      => $booking_list_expired,
            'alat_test_all'             => $alat_test_all,
            'alat_test_pending'         => $alat_test_pending,
            'alat_test_disetujui'       => $alat_test_disetujui,
            'alat_test_dikembalikan'    => $alat_test_dikembalikan,
            'alat_test_ditolak'         => $alat_test_ditolak,

            'room'                      => $room,
            'alat_test'                 => $alat_test,
            'user'                      => $user,
        ]);
    }
}
