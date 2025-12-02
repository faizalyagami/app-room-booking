<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MyBookingAlatTestListRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Jobs\SendEmail;

use App\Models\AlatTestBooking;
use App\Models\AlatTestItem;
use App\Models\AlatTestItemBooking;
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

    public function index()
    {
        return view('pages.user.my-booking-alat-test-list.index');
    }

    public function create()
    {
        $nowdate = Carbon::now();
        $hours = [
            '00','01','02','03','04','05','06','07','08','09',
            '10','11','12','13','14','15','16','17','18','19',
            '20','21','22','23'
        ];
        $minutes = ['00','30'];
        $items = AlatTestItem::with(['alatTest'])->orderBy('serial_number')->get();

        return view('pages.user.my-booking-alat-test-list.create', [
            'hours' => $hours,
            'minutes' => $minutes,
            'nowdate' => $nowdate,
            'items' => $items,
        ]);
    }

    /**
     * Method untuk cek ketersediaan alat - INI YANG HARUS DITAMBAHKAN!
     */
    private function checkAvailability($date, $startTime, $endTime, $itemIds, $excludeId = null)
    {
        $conflicts = AlatTestItemBooking::whereHas('alatTestBooking', function($query) use ($date, $startTime, $endTime, $excludeId) {
            $query->where('date', $date)
                ->where('status', '!=', 'BATAL')
                ->where('status', '!=', 'DITOLAK')
                ->where('status', '!=', 'EXPIRED')
                ->where(function($q) use ($startTime, $endTime) {
                    $q->where(function($q1) use ($startTime, $endTime) {
                        $q1->where('start_time', '<', $endTime)
                           ->where('end_time', '>', $startTime);
                    });
                });
                
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        })
        ->whereIn('alat_test_item_id', $itemIds)
        ->get();

        return $conflicts;
    }

    public function store(MyBookingAlatTestListRequest $request)
    {   
        // Validasi tambahan: waktu end harus > waktu start
        $startTime = implode(':', $request->time_start);
        $endTime = implode(':', $request->time_end);

        if ($endTime <= $startTime) {
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Waktu selesai harus lebih besar dari waktu mulai');
        }

        $bookingDateTimeStart = Carbon::parse($request->date . ' ' . $startTime);
        $now = Carbon::now();

        if ($bookingDateTimeStart->lte($now)) {
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Waktu mulai booking tidak boleh sudah lewat.');
        }

        // Validasi waktu tidak boleh sudah lewat
        $bookingDateTime = Carbon::parse($request->date . ' ' . implode(':', $request->time_start));
        
        if ($bookingDateTime->lte($now)) {
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Tidak dapat booking di waktu yang sudah lewat.');
        }
        
        // Cek ketersediaan alat
        $conflicts = $this->checkAvailability(
            $request->date, 
            $startTime, 
            $endTime, 
            $request->items
        );
        
        if ($conflicts->count() > 0) {
            $conflictItems = $conflicts->map(function($conflict) {
                return $conflict->alatTestItem->serial_number;
            })->unique()->implode(', ');
            
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Beberapa alat tidak tersedia pada waktu yang dipilih: ' . $conflictItems);
        }
        
        $message = new AlatTestBooking();
        $message->user_id = auth()->user()->id;
        $message->date = $request->date;
        $message->start_time = $startTime;
        $message->end_time = $endTime;
        $message->status = 'PENDING';
        $message->purpose = $request->purpose;
        $message->save();

        foreach($request->items as $item) {
            $messageItem = new AlatTestItemBooking();
            $messageItem->booking_alat_id = $message->id;
            $messageItem->alat_test_item_id = $item;
            $messageItem->save();
        }

        // Ambil data user & admin
        $userName  = auth()->user()->name;
        $userEmail = auth()->user()->email;
        $admin     = User::where('role', 'ADMIN')->first();
        $adminEmail = $admin->email;
        $adminName  = $admin->name;

        $items = $message->alatTestItemBooking->map(function($row) {
            return [
                'name' => $row->alatTestItem->alatTest->name,
                'serial' => $row->alatTestItem->serial_number,
            ];
        })->toArray();

        // Siapkan payload untuk job (alat_test)
        $payloadForAdmin = [
            'user_name'     => $userName,
            'items'         => $items,
            'date'          => $message->date,
            'start_time'    => $message->start_time,
            'end_time'      => $message->end_time,
            'purpose'       => $message->purpose,
            'to_role'       => 'ADMIN',
            'receiver_name' => $adminName,
            'url'           => url('/admin/alat-test-booking-list/'.$message->id),
            'status'        => $message->status,
        ];

        $payloadForUser = $payloadForAdmin;
        $payloadForUser['to_role'] = 'USER';
        $payloadForUser['receiver_name'] = $userName;
        $payloadForUser['url'] = url('/my-booking-alat-test-list/'.$message->id);

        // Dispatch job (alat_test)
        dispatch(new SendEmail($adminEmail, 'alat_test', $payloadForAdmin));
        dispatch(new SendEmail($userEmail, 'alat_test', $payloadForUser));

        $request->session()->flash('alert-success', 'Booking alat test berhasil ditambahkan');
        return redirect()->route('my-booking-alat-test-list.index');
    }

    public function show($id)
    {
        $tool = AlatTestBooking::whereId($id)
            ->with(['alatTestItemBooking.alatTestItem.alatTest'])
            ->first();

        return view('pages.user.my-booking-alat-test-list.show', compact('tool'));
    }

    public function edit($id)
    {
        $nowdate = Carbon::now();
        $hours = [
            '00','01','02','03','04','05','06','07','08','09',
            '10','11','12','13','14','15','16','17','18','19',
            '20','21','22','23'
        ];
        $minutes = ['00','30'];
        $tool = AlatTestBooking::whereId($id)
            ->with(['alatTestItemBooking.alatTestItem.alatTest'])
            ->first();

        return view('pages.user.my-booking-alat-test-list.edit', compact(
            'nowdate', 'hours', 'minutes', 'tool'
        ));
    }

    public function update(MyBookingAlatTestListRequest $request, $id)
    {
        $message = AlatTestBooking::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
            
        if ($message === null) {
            $request->session()->flash('alert-failed', 'Booking tidak ditemukan.');
            return redirect()->route('my-booking-alat-test-list.index');
        }
        
        // Cek jika booking sudah lewat
        $bookingDateTimeEnd = Carbon::parse($message->date . ' ' . $message->end_time);
        $now = Carbon::now();
        
        if ($bookingDateTimeEnd->lte($now) && $message->status !== 'BATAL') {
            $request->session()->flash('alert-failed', 'Tidak dapat mengupdate booking yang sudah lewat.');
            return redirect()->route('my-booking-alat-test-list.index');
        }
        
        // Validasi waktu tidak boleh sudah lewat
        $bookingDateTime = Carbon::parse($request->date . ' ' . implode(':', $request->time_start));
        
        if ($bookingDateTime->lte($now)) {
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Tidak dapat booking di waktu yang sudah lewat.');
        }
        
        // Cek ketersediaan alat
        $startTime = implode(':', $request->time_start);
        $endTime = implode(':', $request->time_end);
        
        // Validasi waktu selesai harus > waktu mulai
        if ($endTime <= $startTime) {
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Waktu selesai harus lebih besar dari waktu mulai.');
        }
        
        $conflicts = $this->checkAvailability(
            $request->date, 
            $startTime, 
            $endTime, 
            $request->items,
            $id
        );
        
        if ($conflicts->count() > 0) {
            $conflictItems = $conflicts->map(function($conflict) {
                return $conflict->alatTestItem->serial_number;
            })->unique()->implode(', ');
            
            return redirect()->back()
                ->withInput()
                ->with('alert-failed', 'Beberapa alat tidak tersedia pada waktu yang dipilih: ' . $conflictItems);
        }
        
        // Update data
        $message->date = $request->date;
        $message->start_time = $startTime;
        $message->end_time = $endTime;
        $message->status = 'PENDING'; // Reset status ke pending saat diupdate
        $message->purpose = $request->purpose;
        $message->save();

        // Hapus item booking lama
        AlatTestItemBooking::where('booking_alat_id', $id)->delete();

        // Tambahkan item booking baru
        foreach($request->items as $item) {
            $messageItem = new AlatTestItemBooking();
            $messageItem->booking_alat_id = $message->id;
            $messageItem->alat_test_item_id = $item;
            $messageItem->save();
        }

        // Kirim email notifikasi update
        $userName  = auth()->user()->name;
        $userEmail = auth()->user()->email;
        $admin     = User::where('role', 'ADMIN')->first();
        $adminEmail = $admin->email;
        $adminName  = $admin->name;

        $items = $message->alatTestItemBooking->map(function($row) {
            return [
                'name' => $row->alatTestItem->alatTest->name,
                'serial' => $row->alatTestItem->serial_number,
            ];
        })->toArray();

        // Siapkan payload untuk job (alat_test)
        $payloadForAdmin = [
            'user_name'     => $userName,
            'items'         => $items,
            'date'          => $message->date,
            'start_time'    => $message->start_time,
            'end_time'      => $message->end_time,
            'purpose'       => $message->purpose,
            'to_role'       => 'ADMIN',
            'receiver_name' => $adminName,
            'url'           => url('/admin/alat-test-booking-list/'.$message->id),
            'status'        => 'DIUPDATE',
        ];

        $payloadForUser = $payloadForAdmin;
        $payloadForUser['to_role'] = 'USER';
        $payloadForUser['receiver_name'] = $userName;
        $payloadForUser['url'] = url('/my-booking-alat-test-list/'.$message->id);

        // Dispatch job (alat_test)
        dispatch(new SendEmail($adminEmail, 'alat_test', $payloadForAdmin));
        dispatch(new SendEmail($userEmail, 'alat_test', $payloadForUser));

        $request->session()->flash('alert-success', 'Booking alat test berhasil diupdate');
        return redirect()->route('my-booking-alat-test-list.show', [ $id ]);
    }

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