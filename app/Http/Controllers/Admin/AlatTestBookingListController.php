<?php 
namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller;
use App\Mail\AlatTestBookingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\URL; 
use App\Models\AlatTestBooking; 
use App\Models\AlatTestItemBooking; 
use App\Mail\BookingToolMail; 
use DataTables; 
use Carbon\Carbon; 

class AlatTestBookingListController extends Controller 
{ 
    public function json() { 
        $data = AlatTestBooking::with(['user'])
            ->orderByRaw("CASE 
                WHEN status = 'PENDING' THEN 1
                WHEN status = 'DISETUJUI' THEN 2
                WHEN status = 'DITOLAK' THEN 3
                WHEN status = 'EXPIRED' THEN 4
                WHEN status = 'DIKEMBALIKAN' THEN 5
                WHEN status = 'BATAL' THEN 6
                ELSE 7 END")
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $result = $data->map(function ($item, $index) {
                // Gunakan accessor untuk mendapatkan status otomatis
                $status = $item->status;
                $badgeClass = '';
                
                switch($status) {
                    case 'PENDING':
                        $badgeClass = 'warning';
                        break;
                    case 'DISETUJUI':
                        $badgeClass = 'success';
                        break;
                    case 'DITOLAK':
                        $badgeClass = 'danger';
                        break;
                    case 'EXPIRED':
                        $badgeClass = 'secondary';
                        break;
                    case 'DIKEMBALIKAN':
                        $badgeClass = 'info';
                        break;
                    case 'BATAL':
                        $badgeClass = 'dark';
                        break;
                    default:
                        $badgeClass = 'info';
                }
                
                $statusHtml = '<span class="badge badge-'.$badgeClass.'">'.strtoupper($status).'</span>';
                
                return [ 
                    'index' => $index + 1, 
                    'id' => $item->id, 
                    'user' => $item->user->name ?? '-', 
                    'date' => $item->date, 
                    'start_time' => $item->start_time, 
                    'end_time' => $item->end_time, 
                    'purpose' => $item->purpose, 
                    'status' => $statusHtml,
                    'raw_status' => $status, // Untuk sorting/filter
                ];
            });

            return response()->json(['data' => $result ]); 
    }

    public function index() { 
        // Jalankan pengecekan expired otomatis setiap kali halaman diakses
        $this->checkAndUpdateExpiredBookings();
        
        return view('pages.admin.alat-test-booking-list.index');
    }
    
    /**
     * Method untuk mengecek dan update booking yang expired
     */
    private function checkAndUpdateExpiredBookings()
    {
        // Cari semua booking dengan status PENDING yang tanggal bookingnya sudah lewat
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        
        $expiredBookings = AlatTestBooking::where('status', 'PENDING')
            ->whereDate('date', '<=', $yesterday)
            ->get();
        
        foreach ($expiredBookings as $booking) {
            // Update status menjadi EXPIRED
            $booking->status = 'EXPIRED';
            $booking->save();
            
            // Kirim notifikasi email (opsional)
            $this->sendExpiredNotification($booking);
        }
    }
    
    /**
     * Kirim notifikasi expired ke user (opsional)
     */
    private function sendExpiredNotification($booking)
    {
        try {
            $user_email = $booking->user->email;
            $user_name = $booking->user->name;
            
            $items = $booking->alatTestItemBooking->map(function($row) {
                return [
                    'name'   => $row->alatTestItem->alatTest->nama_alat ?? $row->alatTestItem->alatTest->name,
                    'serial' => $row->alatTestItem->serial_number,
                ];
            })->toArray();
            
            Mail::to($user_email)->send(new AlatTestBookingMail(
                $user_name,
                $items,
                $booking->date,
                $booking->start_time,
                $booking->end_time,
                $booking->purpose,
                'USER',
                $user_name,
                url('/my-booking-alat-test-list/'.$booking->id),
                'EXPIRED'
            ));
        } catch (\Exception $e) {
            // Log error jika email gagal dikirim
            \Log::error('Gagal mengirim email expired notification: ' . $e->getMessage());
        }
    }

    public function show($id) 
    { 
        $booking = AlatTestBooking::with([ 
                'alatTestItemBooking.alatTestItem.alatTest', 
                'user'
            ]) 
            ->whereId($id) 
            ->first();
            
        if (!$booking) {
            session()->flash('alert-failed', 'Booking tidak ditemukan.');
            return redirect()->route('alat-test-booking-list.index');
        }

        return view('pages.admin.alat-test-booking-list.show', compact('booking'));
    }

    public function update($id, $value) 
    {
        $item = AlatTestBooking::with(['alatTestItemBooking.alatTestItem.alatTest', 'user'])
            ->findOrFail($id);

        // Cek jika booking sudah EXPIRED
        if ($item->status === 'EXPIRED') {
            session()->flash('alert-failed', 'Tidak dapat mengupdate booking yang sudah EXPIRED');
            return redirect()->route('alat-test-booking-list.index');
        }
        
        // Cek jika booking sudah BATAL
        if ($item->status === 'BATAL') {
            session()->flash('alert-failed', 'Tidak dapat mengupdate booking yang sudah BATAL');
            return redirect()->route('alat-test-booking-list.index');
        }

        $today = Carbon::today()->toDateString();
        $now   = Carbon::now()->toTimeString();

        $user_name  = $item->user->name;
        $user_email = $item->user->email;
        $admin_name = Auth::user()->name;
        $admin_email = Auth::user()->email;

        if ($value == 1) {
            $data['status'] = 'DISETUJUI';
        } else if ($value == 2) {
            $data['status'] = 'DIKEMBALIKAN';
        } else if ($value == 0) {
            $data['status'] = 'DITOLAK';
        } else {
            session()->flash('alert-failed', 'Perintah tidak dimengerti');
            return redirect()->route('alat-test-booking-list.index');
        }

        // Validasi: hanya bisa update jika booking belum lewat
        // Kecuali untuk status DITOLAK yang bisa dilakukan kapan saja
        if ($data['status'] === 'DITOLAK' || 
            $item['date'] > $today || 
            ($item['date'] == $today && $item['start_time'] > $now)) {
            
            if ($item->update($data)) { 
                session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']); 
                
                // AMBIL SEMUA ALAT + SERIAL (ARRAY)
                $items = $item->alatTestItemBooking->map(function($row) {
                    return [
                        'name'   => $row->alatTestItem->alatTest->nama_alat ?? $row->alatTestItem->alatTest->name,
                        'serial' => $row->alatTestItem->serial_number,
                    ];
                })->toArray();

                // Email ke USER
                Mail::to($user_email)->send(new AlatTestBookingMail(
                    $user_name,
                    $items,
                    $item->date,
                    $item->start_time,
                    $item->end_time,
                    $item->purpose,
                    'USER',
                    $admin_name,
                    url('/my-booking-alat-test-list/'.$item->id),
                    $data['status']
                ));

                // Email ke ADMIN
                Mail::to($admin_email)->send(new AlatTestBookingMail(
                    $user_name,
                    $items,
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
    
    /**
     * Method untuk force update status ke EXPIRED (manual)
     */
    public function expire($id)
    {
        $item = AlatTestBooking::with(['alatTestItemBooking.alatTestItem.alatTest', 'user'])
            ->findOrFail($id);
            
        // Hanya bisa expire booking yang masih PENDING
        if ($item->status !== 'PENDING') {
            session()->flash('alert-failed', 'Hanya booking dengan status PENDING yang bisa di-expire');
            return redirect()->route('alat-test-booking-list.show', $id);
        }
        
        $item->status = 'EXPIRED';
        $item->save();
        
        // Kirim notifikasi
        $user_name  = $item->user->name;
        $user_email = $item->user->email;
        
        $items = $item->alatTestItemBooking->map(function($row) {
            return [
                'name'   => $row->alatTestItem->alatTest->nama_alat ?? $row->alatTestItem->alatTest->name,
                'serial' => $row->alatTestItem->serial_number,
            ];
        })->toArray();
        
        // Email ke USER
        Mail::to($user_email)->send(new AlatTestBookingMail(
            $user_name,
            $items,
            $item->date,
            $item->start_time,
            $item->end_time,
            $item->purpose,
            'USER',
            Auth::user()->name,
            url('/my-booking-alat-test-list/'.$item->id),
            'EXPIRED'
        ));
        
        session()->flash('alert-success', 'Booking berhasil di-expire');
        return redirect()->route('alat-test-booking-list.show', $id);
    }
    
    /**
     * Method untuk menampilkan semua booking yang expired
     */
    public function expired()
    {
        $expiredBookings = AlatTestBooking::with(['user'])
            ->where('status', 'EXPIRED')
            ->orWhere(function($query) {
                $query->where('status', 'PENDING')
                    ->whereDate('date', '<', Carbon::today());
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
            
        return view('pages.admin.alat-test-booking-list.expired', compact('expiredBookings'));
    }

}