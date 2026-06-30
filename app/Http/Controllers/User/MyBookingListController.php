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
// use DataTables;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\BookingCreatedNotification;

class MyBookingListController extends Controller
{
    public function json()
    {
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
        try {
            \Log::info('Store booking started', $request->all());

            $room = Room::select('name')->where('id', $request->room_id)->firstOrFail();

            $startTime = $request->start_time;
            $endTime = $request->end_time;

            // CEK OVERLAP
            $isOverlap = BookingList::where('date', $request->date)
                ->where('room_id', $request->room_id)
                ->whereIn('status', ['DISETUJUI', 'BOOKING_BY_LAB'])
                ->where(function ($q) use ($startTime, $endTime) {
                    // Kondisi 1: Booking yang dimulai di antara waktu booking
                    $q->orWhereBetween('start_time', [$startTime, $endTime]);
                    // Kondisi 2: Booking yang berakhir di antara waktu booking
                    $q->orWhereBetween('end_time', [$startTime, $endTime]);
                    // Kondisi 3: Booking yang mencakup seluruh waktu booking
                    $q->orWhere(function ($sub) use ($startTime, $endTime) {
                        $sub->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
                })
                ->exists();

            if ($isOverlap) {
                return redirect()->route('my-booking-list.create')
                    ->with('alert-failed', 'Ruangan ' . $room->name . ' di waktu itu sudah dibooking');
            }

            // CEK WAKTU MULAI SUDAH LEWAT
            $bookingDateTimeStart = Carbon::parse($request->date . ' ' . $startTime);
            $bookingDateTimeEnd = Carbon::parse($request->date . ' ' . $endTime);
            $now = Carbon::now();

            if ($bookingDateTimeStart->lessThanOrEqualTo($now)) {
                return redirect()->route('my-booking-list.create')
                    ->with('alert-failed', 'Tidak bisa booking untuk waktu yang sudah lewat. Silakan pilih jadwal yang akan datang.');
            }

            if ($bookingDateTimeEnd->lessThanOrEqualTo($now)) {
                return redirect()->route('my-booking-list.create')
                    ->with('alert-failed', 'Tanggal dan waktu yang dipilih sudah terlewat. Silahkan pilih waktu yang masih tersedia.');
            }

            $today = Carbon::today();
            $bookingDate = Carbon::parse($request->date);

            if ($bookingDate->lessThan($today)) {
                return redirect()->route('my-booking-list.create')
                    ->with('alert-failed', 'Tidak bisa booking untuk tanggal yang sudah lewat.');
            }

            // SIMPAN BOOKING
            $message = new BookingList();
            $message->room_id = $request->room_id;
            $message->user_id = auth()->user()->id;
            $message->date = $request->date;
            $message->start_time = $startTime;
            $message->end_time = $endTime;
            $message->status = 'PENDING';
            $message->purpose = $request->purpose;
            $message->save();

            \Log::info('Booking saved successfully', ['id' => $message->id]);

            auth()->user()->notify(new BookingCreatedNotification($message));

            $adminUser = User::where('role', 'ADMIN')->first();
            if ($adminUser) {
                $adminUser->notify(new BookingCreatedNotification($message));
            }

            return redirect()->route('my-booking-list.index')
                ->with('alert-success', 'Booking ruang ' . $room->name . ' berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error in store booking: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('my-booking-list.create')
                ->with('alert-failed', 'Terjadi kesalahan: ' . $e->getMessage());
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

        if ($item->update($data)) {
            session()->flash('alert-success', 'Booking Ruang ' . $room->name . ' berhasil dibatalkan');

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
            session()->flash('alert-failed', 'Booking Ruang ' . $room->name . ' gagal dibatalkan');
        }

        return redirect()->route('my-booking-list.index');
    }

    public function getAdminData()
    {
        return User::select('name', 'email')->where('role', 'ADMIN')->firstOrFail();
    }

    public function getUserName()
    {
        return Auth::user()->name;
    }

    public function getUserEmail()
    {
        return Auth::user()->email;
    }

    public function rules()
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'room_id.required' => 'Ruangan wajib dipilih',
            'room_id.exists' => 'Ruangan tidak valid',
            'date.required' => 'Tanggal booking wajib diisi',
            'date.date' => 'Format tanggal tidak valid',
            'start_time.required' => 'Jam mulai wajib diisi',
            'start_time.date_format' => 'Format jam mulai tidak valid (HH:MM)',
            'end_time.required' => 'Jam selesai wajib diisi',
            'end_time.date_format' => 'Format jam selesai tidak valid (HH:MM)',
            'end_time.after' => 'Jam selesai harus setelah jam mulai',
            'purpose.required' => 'Keperluan wajib diisi',
        ];
    }
}
