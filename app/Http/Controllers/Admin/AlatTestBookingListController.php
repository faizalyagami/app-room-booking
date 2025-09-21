<?php 
namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\URL; 
use App\Models\AlatTestBooking; 
use App\Models\AlatTestItemBooking; 
use App\Mail\BookingToolMail; 
use DataTables; 
use Carbon\Carbon; 

class AlatTestBookingListController extends Controller { 
    public function json() { 
        $data = AlatTestBooking::with(['user'])->get();

        $result = $data->map(function ($item, $index) {
            return [ 
                'index' => $index + 1, 
                'id' => $item->id, 
                'user' => $item->user->name ?? '-', 
                'date' => $item->date, 
                'start_time' => $item->start_time, 
                'end_time' => $item->end_time, 
                'purpose' => $item->purpose, 
                'status' => $item->status, ];
             }); 
             return response()->json([ 
                'data' => $result ]); 
            } 
    public function index() { 
        return view('pages.admin.alat-test-booking-list.index');
    } 
    public function show($id) { 
        $booking = AlatTestBooking::with([ 
            'alatTestItemBooking.alatTestItem.alatTest', 
            'user' ]) 
            ->whereId($id) 
            ->first(); 
        return view('pages.admin.alat-test-booking-list.show', compact('booking'));
    } 
    public function update($id, $value) 
{
    $item = AlatTestBooking::with(['alatTestItemBooking.alatTestItem.alatTest', 'user'])
        ->findOrFail($id);

    $today = Carbon::today()->toDateString();
    $now   = Carbon::now()->toTimeString();

    $user_name  = $item->user->name;
    $user_email = $item->user->email;
    $admin_name = Auth::user()->name;
    $admin_email= Auth::user()->email;

    if ($value == 1) { 
        $data['status'] = 'DISETUJUI';
    } else if ($value == 0) { 
        $data['status'] = 'DITOLAK';
    } else { 
        session()->flash('alert-failed', 'Perintah tidak dimengerti');
        return redirect()->route('alat-test-booking-list.index');
    }

    if ($item['date'] > $today || ($item['date'] == $today && $item['start_time'] > $now)) {
        if ($item->update($data)) { 
            session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']); 
            
            $alatName = $item->alatTestItemBooking->first()->alatTestItem->alatTest->nama_alat ?? 'Alat Test'; 
            
            // Email ke USER
            Mail::to($user_email)->send(new BookingToolMail(
                $user_name,
                $alatName,
                $item->date,
                $item->start_time,
                $item->end_time,
                $item->purpose,
                'USER',
                $user_name,
                url('/my-booking-alat-test-list/'.$item->id),
                $data['status']
            ));

            // Email ke ADMIN
            Mail::to($admin_email)->send(new BookingToolMail(
                $user_name,
                $alatName,
                $item->date,
                $item->start_time,
                $item->end_time,
                $item->purpose,
                'ADMIN',
                $admin_name,
                url('/admin/alat-test-booking-list/'.$item->id),
                $data['status']
            ));
        } else {
            session()->flash('alert-failed', 'Booking Alat Test gagal diupdate'); 
        }
    } else { 
        session()->flash('alert-failed', 'Permintaan booking itu tidak lagi bisa diupdate');
    }
        return redirect()->route('alat-test-booking-list.index'); 
    }
}