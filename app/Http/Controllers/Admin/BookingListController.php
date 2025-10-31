<?php 
namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\URL; 
use App\Models\BookingList; 
use App\Models\User; 
use App\Jobs\SendEmail; 
use DataTables; 
use Carbon\Carbon; 

class BookingListController extends Controller { 
    public function json() { $data = BookingList::with(['room', 'user'])
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->get(); 
        $result = $data->map(function ($item, $index) { 
            return [
                 'index' => $index + 1, 
                 'id' => $item->id, 
                 'photo' => $item->room->photo ?? '-', 
                 'room' => $item->room->name ?? '-', 
                 'user' => $item->user->name ?? '-', 
                 'date' => $item->date, 
                 'start_time' => $item->start_time, 
                 'end_time' => $item->end_time, 
                 'purpose' => $item->purpose, 
                 'status' => $item->status,
                ]; 
            }); 
                return response()->json([ 'data' => $result ]); 
    } /** * Display a listing of the resource. * * @return \Illuminate\Http\Response */ 
    public function index() { return view('pages.admin.booking-list.index'); 
    } 
    public function update($id, $value) { 
        $item = BookingList::with(['room', 'user'])->findOrFail($id); 
        $today = Carbon::today(); $now = Carbon::now(); 
        $user_name = $item->user->name; $user_email = $item->user->email; 
        $admin_name = Auth::user()->name; $admin_email = Auth::user()->email; 
        // tentukan status 
        if ($value == 1) { 
            $data['status'] = 'DISETUJUI'; 
        } elseif ($value == 0) { $data['status'] = 'DITOLAK'; 
        } else { session()->flash('alert-failed', 'Perintah tidak dimengerti'); 
            return redirect()->route('booking-list.index'); 
        } 
        // validasi waktu booking 
        $bookingStart = Carbon::parse($item->date.' '.$item->start_time); 
        if (!$bookingStart->isFuture()) { session()->flash('alert-failed', 'Permintaan booking itu tidak lagi bisa diupdate'); 
            return redirect()->route('booking-list.index'); 
        } 
        // cek overlap hanya jika disetujui 
        $isOverlap = false; 
        if ($data['status'] == 'DISETUJUI') { 
            $isOverlap = BookingList::where('date', $item->date) ->where('room_id', $item->room_id) ->where('status', 'DISETUJUI') ->where(function ($q) use ($item) { $q->whereBetween('start_time', [$item->start_time, $item->end_time]) ->orWhereBetween('end_time', [$item->start_time, $item->end_time]) ->orWhere(function ($q2) use ($item) { $q2->where('start_time', '<=', $item->start_time) ->where('end_time', '>=', $item->end_time); 
            }); 
        }) ->exists(); 
        } if ($data['status'] == 'DISETUJUI' && $isOverlap) { session()->flash('alert-failed', 'Ruangan '.$item->room->name.' di waktu itu sudah dibooking'); 
                return redirect()->route('booking-list.index'); 
            } 
        // update data 
        if ($item->update($data)) { 
            session()->flash('alert-success', 'Booking Ruang '.$item->room->name.' sekarang '.$data['status']); 
            // daftar penerima email 
            $recipients = [ [ 'role' => 'USER', 'email' => $user_email, 'name' => $user_name, 'url' => URL::to('/my-booking-list'), ], [ 'role' => 'ADMIN', 'email' => $admin_email, 'name' => $admin_name, 'url' => URL::to('/admin/booking-list'), ], ];
            // kirim email ke user & admin 
            foreach ($recipients as $recipient) { 
                dispatch(new SendEmail($recipient['email'], 'room', [
                        'user_name'     => $user_name,
                        'room_name'     => $item->room->name,
                        'date'          => $item->date,
                        'start_time'    => $item->start_time,
                        'end_time'      => $item->end_time,
                        'purpose'       => $item->purpose,
                        'to_role'       => $recipient['role'],
                        'receiver_name' => $recipient['name'],
                        'url'           => $recipient['url'],
                        'status'        => $data['status'],
                    ]));
                 }
                } else { 
                    session()->flash('alert-failed', 'Booking Ruang '.$item->room->name.' gagal diupdate'); 
                } return redirect()->route('booking-list.index');
            }
         }