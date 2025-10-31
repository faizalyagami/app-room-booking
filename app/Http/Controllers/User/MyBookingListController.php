<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\User;

use App\Jobs\SendEmail;

use App\Http\Requests\User\MyBookingListRequest;
use App\Models\DayTime;
use Carbon\Carbon;
use DataTables;

class MyBookingListController extends Controller
{
    public function json(){
        $data = BookingList::where('user_id', Auth::user()->id)->with([
            'room'
        ]);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.user.my-booking-list.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nowdate = Carbon::now();
        $rooms = Room::orderBy('name')->get();
        $times = DayTime::distinct()->select('start_time', 'end_time')
            ->whereNull('deleted_at')
            ->orderBy('start_time')
            ->get();

        return view('pages.user.my-booking-list.create', [
            'rooms' => $rooms, 
            'times' => $times, 
            'nowdate' => $nowdate, 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyBookingListRequest $request)
    {
        $save = 1;
        $time = explode(" - ", $request->time);
        $room = Room::select('name')->where('id', $request->room_id)->firstOrFail();
        $bookings = BookingList::where('date', $request->date)
            ->where('room_id', $request->room_id)
            ->where('status', 'DISETUJUI')
            ->get();

        if(count($bookings) > 0) {
            foreach($bookings as $booking) {
                if(date("H:i:s", strtotime($booking->start_time)) == date("H:i:s", strtotime($time[0]))) {
                    $save = 0;
                }
            }
        }

        if($save == 0 ) {
            $request->session()->flash('alert-failed', 'Ruangan '.$room->name.' di waktu itu sudah dibooking');
            return redirect()->route('my-booking-list.create');
        } 

        $bookingDateTimeStart = Carbon::parse($request->date . ' ' . $time[0]);
        $bookingDateTimeEnd = Carbon::parse($request->date . ' ' . $time[1]);
        $now = Carbon::now();

        if ($bookingDateTimeEnd->lessThanOrEqualTo($now)) {
            $request->session()->flash('alert-failed', 'Tanggal dan waktu yang dipilih sudah terlewat. Silahkan pilih waktu yang masih tersedia.');
            return redirect()->route('my-booking-list.create');
        }

            $message = new BookingList();
            $message->room_id = $request->room_id;
            $message->user_id = auth()->user()->id;
            $message->date = $request->date;
            $message->start_time = $time[0];
            $message->end_time = $time[1];
            $message->status = 'PENDING';
            $message->purpose = $request->purpose;
            $message->save();

            // Ambil data user & admin
            $user   = Auth::user();
            $admin  = $this->getAdminData();
            $status = 'DIBUAT';

            // Email ke USER
            dispatch(new SendEmail(
                [$user->email], 
                'room', // type
                [
                    'user_name'     => $user->name,
                    'room_name'     => $room->name,
                    'date'          => $request->date,
                    'start_time'    => $time[0],
                    'end_time'      => $time[1],
                    'purpose'       => $request->purpose,
                    'to_role'       => 'USER',
                    'receiver_name' => $user->name,
                    'url'           => URL::to('/my-booking-list'),
                    'status'        => $status,
                ]
            ));

            // Email ke ADMIN
            dispatch(new SendEmail(
                [$admin->email], 
                'room', // type
                [
                    'user_name'     => $user->name,
                    'room_name'     => $room->name,
                    'date'          => $request->date,
                    'start_time'    => $time[0],
                    'end_time'      => $time[1],
                    'purpose'       => $request->purpose,
                    'to_role'       => 'ADMIN',
                    'receiver_name' => $admin->name,
                    'url'           => URL::to('/admin/booking-list'),
                    'status'        => $status,
                ]
            ));

            $request->session()->flash('alert-success', 'Booking ruang '.$room->name.' berhasil ditambahkan');
            return redirect()->route('my-booking-list.index');
        
    }

    /**
     * Cancel the specified data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $item           = BookingList::findOrFail($id);
        $data['status'] = 'BATAL';

        $room               = Room::select('name')->where('id', $item->room_id)->firstOrFail();

        if($item->update($data)) {
            session()->flash('alert-success', 'Booking Ruang '.$room->name.' berhasil dibatalkan');

            $user_name          = $this->getUserName();
            $user_email         = $this->getUserEmail();

            $admin      = $this->getAdminData();
            $status     = $data['status'];

            $to_role    = 'USER';

            dispatch(new SendEmail(
                [$user_email],
                'room',
                [
                    'user_name'     => $user_name,
                    'room_name'     => $room->name,
                    'date'          => $item->date,
                    'start_time'    => $item->start_time,
                    'end_time'      => $item->end_time,
                    'purpose'       => $item->purpose,
                    'to_role'       => $to_role,
                    'receiver_name' => $user_name,
                    'url'           => URL::to('/my-booking-list'),
                    'status'        => $status,
                ]
            ));

            dispatch(new SendEmail(
                [$admin->email],
                'room',
                [
                    'user_name'     => $user_name,
                    'room_name'     => $room->name,
                    'date'          => $item->date,
                    'start_time'    => $item->start_time,
                    'end_time'      => $item->end_time,
                    'purpose'       => $item->purpose,
                    'to_role'       => 'ADMIN',
                    'receiver_name' => $admin->name,
                    'url'           => URL::to('/admin/booking-list'),
                    'status'        => $status,
                ]
            ));
        } else {
            session()->flash('alert-failed', 'Booking Ruang '.$room->name.' gagal dibatalkan');
        }
        
        return redirect()->route('my-booking-list.index');
    }

    public function getAdminData() {
        return User::select('name','email')->where('role', 'ADMIN')->firstOrFail();
    }

    public function getUserName() {
        return Auth::user()->name;
    }

    public function getUserEmail() {
        return Auth::user()->email;
    }
}
