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
    public function json() { 
        $this->updateBookingStatus();

        $data = BookingList::with(['room', 'user'])
        ->orderByRaw("
            CASE 
                WHEN status = 'PENDING' THEN 1
                WHEN status = 'DITOLAK' THEN 2
                WHEN status = 'DISETUJUI' THEN 3
                WHEN status = 'DIGUNAKAN' THEN 4
                WHEN status = 'SELESAI' THEN 5
                WHEN status = 'EXPIRED' THEN 6
                WHEN status = 'BOOKING_BY_LAB' THEN 7
                ELSE 8
            END
            ")
        ->orderBy('date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get(); 
        $result = $data->map(function ($item, $index) {
            $date = Carbon::parse($item->date);
            $day = $this->getIndonesianDay($date->dayOfWeek);
            $formattedDate = $item->date . '-' . $day; 
            return [
                 'index' => $index + 1, 
                 'id' => $item->id, 
                 'photo' => $item->room->photo ?? '-', 
                 'room' => $item->room->name ?? '-', 
                 'user' => $item->user->name ?? '-', 
                 'date' => $item->date,
                 'date_display' => $formattedDate,
                 'start_time' => $item->start_time, 
                 'end_time' => $item->end_time, 
                 'purpose' => $item->purpose, 
                 'status' => $item->status,
                ]; 
            }); 
                return response()->json([ 'data' => $result ]); 
    } /** * Display a listing of the resource. * * @return \Illuminate\Http\Response */

    private function updateBookingStatus() {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();
        $oneDayAgo = $now->copy()->subDay();

        try {
            //1. booking sedang berlangsung: disetujui -> digunakan
            $ongoingBookings = BookingList::where('date', $today)
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->where('status', 'DISETUJUI')
                ->update(['status' => 'DIGUNAKAN']);
            // 2. Booking sudah selesai (DISETUJUI/DIGUNAKAN -> SELESAI)
            // Selesai untuk booking hari ini yang sudah lewat end_time
            $todayCompleted = BookingList::where('date', $today)
                ->where('end_time', '<', $currentTime)
                ->whereIn('status', ['DISETUJUI', 'DIGUNAKAN'])
                ->update(['status' => 'SELESAI']);

            // Selesai untuk booking di tanggal sebelumnya
            $pastCompleted = BookingList::where('date', '<', $today)
                ->whereIn('status', ['DISETUJUI', 'DIGUNAKAN'])
                ->update(['status' => 'SELESAI']);

            // 3. HANYA booking yang dibuat lebih dari 1 hari dan masih PENDING -> EXPIRED
            $unapprovedExpired = BookingList::where('status', 'PENDING')
                ->where('created_at', '<=', $oneDayAgo)
                ->update(['status' => 'EXPIRED']);
            
            \Log::info("Auto-update status: {$ongoingBookings} ongoing, {$todayCompleted} today completed, {$pastCompleted} past completed, {$unapprovedExpired} unapproved expired (1 day)");
            } catch (\Exception $e) {
                \Log::error('Error updating booking status: ' . $e->getMessage());
            }
    }

    private function getIndonesianDay($dayOfWeek) {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        return $days[$dayOfWeek] ?? '-';
    }
    public function index() { return view('pages.admin.booking-list.index'); 
    } 
    public function update($id, $value) { 
        $item = BookingList::with(['room', 'user'])->findOrFail($id); 
        $today = Carbon::today(); 
        $now = Carbon::now(); 
        $user_name = $item->user->name; 
        $user_email = $item->user->email; 
        $admin_name = Auth::user()->name; 
        $admin_email = Auth::user()->email;

        //CEK APAKAH BOOKING SUDAH EXPIRED/SELESAI (tidak bisa di-update)
        $bookingDateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', $item->date . ' ' . $item->start_time);
        $bookingDateTimeEnd = Carbon::createFromFormat('Y-m-d H:i:s', $item->date . ' ' . $item->end_time);

        if ($now->greaterThan($bookingDateTimeEnd)) {
            session()->flash('alert-failed', 'Tidak bisa mengupdate booking yang sudah EXPIRED/SELESAI');
            return redirect()->route('booking-list.index');
        }

        if ($now->greaterThanOrEqualTo($bookingDateTimeStart)) {
            session()->flash('alert-failed', 'Tidak bisa mengupdate booking yang sedang DIGUNAKAN');
            return redirect()->route('booking-list.index');
        }

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
            $isOverlap = BookingList::where('date', $item->date) 
            ->where('room_id', $item->room_id)
            ->where('id', '!=', $item->id)
            ->whereIn('status', ['DISETUJUI', 'BOOKING_BY_LAB']) 
            ->where(function ($q) use ($item) { 
                $q->whereBetween('start_time', [$item->start_time, $item->end_time]) 
                ->orWhereBetween('end_time', [$item->start_time, $item->end_time]) 
                ->orWhere(function ($q2) use ($item) { 
                    $q2->where('start_time', '<=', $item->start_time) 
                    ->where('end_time', '>=', $item->end_time); 
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