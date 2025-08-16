<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MyBookingAlatTestListRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\User;

use App\Jobs\SendEmail;

use App\Models\AlatTestBooking;
use App\Models\AlatTestItem;
use App\Models\AlatTestItemBooking;
use App\Models\DayTime;
use Carbon\Carbon;
use DataTables;

class AlatTestBookingController extends Controller
{
    public function json(){
        $data = AlatTestBooking::where('user_id', Auth::user()->id)->with([
            'alatTestItemBooking.alatTestItem.alatTest'
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
        return view('pages.user.my-booking-alat-test-list.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nowdate = Carbon::now();
        $hours = [
            '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', 
            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
            '20', '21', '22', '23' 
        ];
        $minutes = [
            '00', '30'
        ];
        $items = AlatTestItem::with(['alatTest'])->orderBy('serial_number')->get();

        return view('pages.user.my-booking-alat-test-list.create', [
            'hours' => $hours, 
            'minutes' => $minutes, 
            'nowdate' => $nowdate, 
            'items' => $items, 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyBookingAlatTestListRequest $request)
    {
        $message = new AlatTestBooking();
        $message->user_id = auth()->user()->id;
        $message->date = $request->date;
        $message->start_time = implode(":", $request->time_start);
        $message->end_time = implode(":", $request->time_end);
        $message->status = 'PENDING';
        $message->purpose = $request->purpose;
        $message->save();

        foreach($request->items as $item) {
            $messageItem = new AlatTestItemBooking();
            $messageItem->booking_alat_id = $message->id;
            $messageItem->alat_test_item_id= $item;
            $messageItem->save();
        }

        $request->session()->flash('alert-success', 'Booking alat test berhasil ditambahkan');
        return redirect()->route('my-booking-alat-test-list.index');
    }

    public function show($id) 
    {
        $tool = AlatTestBooking::whereId($id)
            ->with(['alatTestItemBooking.alatTestItem.alatTest'])
            ->first();

        return view('pages.user.my-booking-alat-test-list.show', compact(
            'tool'
        ));
    }

    public function edit($id) 
    {
        $nowdate = Carbon::now();
        $hours = [
            '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', 
            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
            '20', '21', '22', '23' 
        ];
        $minutes = [
            '00', '30'
        ];
        $tool = AlatTestBooking::whereId($id)
            ->with(['alatTestItemBooking.alatTestItem.alatTest'])
            ->first();

        return view('pages.user.my-booking-alat-test-list.edit', compact(
            'nowdate', 'hours', 'minutes', 'tool'
        ));
    }

    public function update(MyBookingAlatTestListRequest $request, $id)
    {
        $message = AlatTestBooking::whereId($id)->first();
        if($message !== null) {
            $message->user_id = auth()->user()->id;
            $message->date = $request->date;
            $message->start_time = implode(":", $request->time_start);
            $message->end_time = implode(":", $request->time_end);
            $message->status = 'PENDING';
            $message->purpose = $request->purpose;
            $message->save();

            AlatTestItemBooking::where('booking_alat_id', $id)->delete();

            foreach($request->items as $item) {
                $messageItem = new AlatTestItemBooking();
                $messageItem->booking_alat_id = $message->id;
                $messageItem->alat_test_item_id= $item;
                $messageItem->save();
            }

            $request->session()->flash('alert-failed', 'Booking alat test berhasil diupdate');
            return redirect()->route('my-booking-alat-test-list.show', [ $id ]);
        }

        $request->session()->flash('alert-failed', 'Booking alat test gagal diupdate');
        return redirect()->route('my-booking-alat-test-list.show', [ $id ]);
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
        $tool = AlatTestBooking::whereId($id)->first();

        if($tool !== null) {
            $tool->status = 'BATAL';
            $tool->save();

            session()->flash('alert-success', 'Booking Alat Test berhasil dibatalkan');
            return redirect()->route('my-booking-alat-test-list.index');
        }

        session()->flash('alert-failed', 'Booking Alat Test gagal dibatalkan');
        return redirect()->route('my-booking-alat-test-list.index');
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
