<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\BookingList;
use App\Models\User;

use App\Jobs\SendEmail;
use App\Models\AlatTestBooking;
use App\Models\AlatTestItemBooking;
use DataTables;
use Carbon\Carbon;

class AlatTestBookingListController extends Controller
{
    public function json()
    {
        $data = AlatTestBooking::with([
                'user'
            ])
            ->get();

        $result = $data->map(function ($item, $index) {
            return [
                'index' => $index + 1,
                'id' => $item->id,
                'user' => $item->user->name ?? '-',
                'date' => $item->date,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'purpose' => $item->purpose,
                'status' => $item->status,
            ];
        });

        return response()->json([
            'data' => $result
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.admin.alat-test-booking-list.index');
    }

    public function show($id)
    {
        $booking = AlatTestBooking::with([
                'alatTestItemBooking.alatTestItem.alatTest', 
                'user'
            ])
            ->whereId($id)
            ->first();

        return view('pages.admin.alat-test-booking-list.show', compact(
            'booking'
        ));
    }

    public function update($id, $value)
    {
        $item   = AlatTestBooking::findOrFail($id);
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now()->toTimeString();

        $user_name          = $item->user->name;
        $user_email         = $item->user->email;

        $admin_name         = Auth::user()->name;
        $admin_email        = Auth::user()->email;

        if ($value == 1) {
            $data['status'] = 'DISETUJUI';
        } else if ($value == 2) {
            $data['status'] = 'DIKEMBALIKAN';
        } else if ($value == 0) {
            $data['status'] = 'DITOLAK';
        } else {
            session()->flash('alert-failed', 'Perintah tidak dimengerti');
            return redirect()->route('booking-list.index');
        }

        if ($item['date'] > $today || ($item['date'] == $today && $item['start_time'] > $now)) {
            if ($data['status'] == 'DISETUJUI') {
                if (
                    AlatTestBooking::where([
                        ['date', '=', $item['date']],
                        ['status', '=', 'DISETUJUI'],
                    ])
                    ->whereBetween('start_time', [$item['start_time'], $item['end_time']])
                    ->count() <= 0 &&
                    AlatTestBooking::where([
                        ['date', '=', $item['date']],
                        ['status', '=', 'DISETUJUI'],
                    ])
                    ->whereBetween('end_time', [$item['start_time'], $item['end_time']])
                    ->count() <= 0 &&
                    AlatTestBooking::where([
                        ['date', '=', $item['date']],
                        ['start_time', '<=', $item['start_time']],
                        ['end_time', '>=', $item['end_time']],
                        ['status', '=', 'DISETUJUI'],
                    ])->count() <= 0
                ) {
                    if ($item->update($data)) {
                        session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']);

                        // $to_role    = 'USER';

                        // // use URL::to('/') for the url value

                        // // URL::to('/my-booking-list)
                        // dispatch(new SendEmail($user_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $user_name, 'https://google.com', $data['status']));

                        // $to_role    = 'ADMIN';

                        // // URL::to('/admin/booking-list)
                        // dispatch(new SendEmail($admin_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $admin_name, 'https://google.com', $data['status']));
                    } else {
                        session()->flash('alert-failed', 'Booking Alat Test gagal diupdate');
                    }
                } else {
                    session()->flash('alert-failed', 'Alat test di waktu itu sudah dibooking');
                }
            } elseif ($data['status'] == 'DITOLAK') {
                if ($item->update($data)) {
                    session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']);

                    // $to_role    = 'USER';

                    // // URL::to('/my-booking-list)
                    // dispatch(new SendEmail($user_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $user_name, 'https://google.com', $data['status']));

                    // $to_role    = 'ADMIN';

                    // // URL::to('/admin/booking-list)
                    // dispatch(new SendEmail($admin_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $admin_name, 'https://google.com', $data['status']));
                } else {
                    session()->flash('alert-failed', 'Booking Alat Test gagal diupdate');
                }
            } elseif ($data['status'] == 'DIKEMBALIKAN') {
                if ($item->update($data)) {
                    session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']);

                    // $to_role    = 'USER';

                    // // URL::to('/my-booking-list)
                    // dispatch(new SendEmail($user_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $user_name, 'https://google.com', $data['status']));

                    // $to_role    = 'ADMIN';

                    // // URL::to('/admin/booking-list)
                    // dispatch(new SendEmail($admin_email, $user_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], $to_role, $admin_name, 'https://google.com', $data['status']));
                } else {
                    session()->flash('alert-failed', 'Booking Alat Test gagal diupdate');
                }
            }
        } else {
            session()->flash('alert-failed', 'Permintaan booking itu tidak lagi bisa diupdate');
        }

        return redirect()->route('alat-test-booking-list.index');
    }
}
