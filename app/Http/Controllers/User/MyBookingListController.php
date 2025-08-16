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
        } else {
            $message = new BookingList();
            $message->room_id = $request->room_id;
            $message->user_id = auth()->user()->id;
            $message->date = $request->date;
            $message->start_time = $time[0];
            $message->end_time = $time[1];
            $message->status = 'PENDING';
            $message->purpose = $request->purpose;
            $message->save();

            $user_name          = $this->getUserName();
            $user_email         = $this->getUserEmail();
            
            $admin      = $this->getAdminData();
            $status     = 'DIBUAT';

            $to_role    = 'USER';

            // use URL::to('/') for the url value

            // URL::to('/my-booking-list)
            dispatch(new SendEmail($user_email, $user_name, $room->name, $data['date'], $data['start_time'], $data['end_time'], $data['purpose'], $to_role, $user_name, 'https://google.com', $status));

            $to_role    = 'ADMIN';

            // URL::to('/admin/booking-list)
            dispatch(new SendEmail($admin->email, $user_name, $room->name, $data['date'], $data['start_time'], $data['end_time'], $data['purpose'], $to_role, $admin->name, 'https://google.com', $status));

            $request->session()->flash('alert-success', 'Booking ruang '.$room->name.' berhasil ditambahkan');
            return redirect()->route('my-booking-list.index');
        }
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

            dispatch(new SendEmail($user_email, $user_name, $room->name, $item->date, $item->start_time, $item->end_time, $item->purpose, $to_role, $user_name, 'https://google.com', $status));
            
            $to_role    = 'ADMIN';

            dispatch(new SendEmail($admin->email, $user_name, $room->name, $item->date, $item->start_time, $item->end_time, $item->purpose, $to_role, $admin->name, 'https://google.com', $status));
            
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
