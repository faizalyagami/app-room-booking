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
use DataTables; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB;

class AlatTestBookingListController extends Controller 
{ 
    public function json(Request $request) { 
        // Update status expired sebelum mengambil data
        $this->updateBookingAlatTestStatus();

        $query = AlatTestBooking::with(['user']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                return '<div class="table-links">
                    <a href="'.route('alat-test-booking-list.show', $row->id).'" class="text-info">Detail</a>
                </div>';
            })
            ->addColumn('date_formatted', function($row) {
                $date = Carbon::parse($row->date);
                $day = $this->getIndonesianDay($date->dayOfWeek);
                return $row->date . ' - ' . $day;
            })
            ->addColumn('status_badge', function($row) {
                $badgeClass = 'badge-';
                
                switch($row->status) {
                    case 'PENDING': $badgeClass .= 'info'; break;
                    case 'DISETUJUI': $badgeClass .= 'success'; break;
                    case 'DITOLAK': $badgeClass .= 'danger'; break;
                    case 'EXPIRED': $badgeClass .= 'warning'; break;
                    case 'BATAL': $badgeClass .= 'warning'; break;
                    case 'DIKEMBALIKAN': $badgeClass .= 'success'; break;
                    default: $badgeClass .= 'secondary';
                }
                
                return '<span class="badge ' . $badgeClass . '">' . $row->status . '</span>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->orderColumn('status', function($query, $order) {
               
                $query->orderByRaw("
                    CASE 
                        WHEN status = 'PENDING' THEN 1
                        WHEN status = 'DISETUJUI' THEN 2
                        WHEN status = 'DITOLAK' THEN 3
                        WHEN status = 'DIKEMBALIKAN' THEN 4
                        WHEN status = 'EXPIRED' THEN 5
                        WHEN status = 'BATAL' THEN 6
                        ELSE 7
                    END $order
                ");
            })
            ->make(true);
    }

   
    private function updateBookingAlatTestStatus() {
        try {
            $now = Carbon::now();
            $today = $now->toDateString(); // Format: YYYY-MM-DD
            
            Log::info("=== START AUTO-UPDATE ALAT TEST STATUS ===");
            Log::info("Current date: {$today}");
            Log::info("Current time: " . $now->toTimeString());
            
            // 1. CEK DATA SEBELUM UPDATE
            $pendingBookings = AlatTestBooking::where('status', 'PENDING')->get();
            Log::info("Total PENDING bookings: " . $pendingBookings->count());
            
            foreach ($pendingBookings as $booking) {
                Log::info("  - ID: {$booking->id}, Date: {$booking->date}, Created: {$booking->created_at}");
            }
            
            // 2. UPDATE SEMUA PENDING YANG TANGGALNYA SUDAH LEWAT
            $expiredCount = DB::update("
                UPDATE alat_test_bookings 
                SET status = 'EXPIRED', updated_at = ? 
                WHERE status = 'PENDING' 
                AND date < ?
            ", [$now, $today]);
            
            Log::info("Expired bookings (date < {$today}): {$expiredCount}");
            
            // 3. UPDATE JUGA PENDING YANG DIBUAT LEBIH DARI 24 JAM YANG LALU
            $twentyFourHoursAgo = $now->copy()->subHours(24);
            $expiredByTime = DB::update("
                UPDATE alat_test_bookings 
                SET status = 'EXPIRED', updated_at = ? 
                WHERE status = 'PENDING' 
                AND created_at <= ?
            ", [$now, $twentyFourHoursAgo]);
            
            Log::info("Expired bookings (>24h old): {$expiredByTime}");
            
            // 4. UPDATE DISETUJUI YANG SUDAH LEWAT -> DIKEMBALIKAN
            $returnedCount = 0;
            
            // 4a. Tanggal kemarin atau sebelumnya
            $pastApproved = AlatTestBooking::where('status', 'DISETUJUI')
                ->where('date', '<', $today)
                ->get();
                
            foreach ($pastApproved as $booking) {
                DB::transaction(function () use ($booking, $now) {
                    $booking->status = 'DIKEMBALIKAN';
                    $booking->updated_at = $now;
                    $booking->save();
                    
                    Log::info("Auto-returned past booking: ID={$booking->id}, Date={$booking->date}");
                    
                    // Update alat
                    foreach ($booking->alatTestItemBooking as $item) {
                        if ($item->alatTestItem) {
                            $item->alatTestItem->status = 'TERSEDIA';
                            $item->alatTestItem->save();
                        }
                    }
                });
                $returnedCount++;
            }
            
            // 4b. Hari ini tapi waktu sudah lewat
            $todayCompleted = AlatTestBooking::where('status', 'DISETUJUI')
                ->where('date', $today)
                ->where('end_time', '<', $now->toTimeString())
                ->get();
                
            foreach ($todayCompleted as $booking) {
                DB::transaction(function () use ($booking, $now) {
                    $booking->status = 'DIKEMBALIKAN';
                    $booking->updated_at = $now;
                    $booking->save();
                    
                    Log::info("Auto-returned today booking: ID={$booking->id}, End={$booking->end_time}");
                    
                    foreach ($booking->alatTestItemBooking as $item) {
                        if ($item->alatTestItem) {
                            $item->alatTestItem->status = 'TERSEDIA';
                            $item->alatTestItem->save();
                        }
                    }
                });
                $returnedCount++;
            }
            
            Log::info("=== AUTO-UPDATE COMPLETED ===");
            Log::info("Total expired: " . ($expiredCount + $expiredByTime));
            Log::info("Total returned: {$returnedCount}");
            
            // 5. CEK DATA SETELAH UPDATE
            $remainingPending = AlatTestBooking::where('status', 'PENDING')->count();
            Log::info("Remaining PENDING bookings: {$remainingPending}");
            
        } catch (\Exception $e) {
            Log::error('ERROR in updateBookingAlatTestStatus: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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

    public function index() { 
        $this->updateBookingAlatTestStatus();
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
            
        if (!$booking) {
            session()->flash('alert-failed', 'Booking tidak ditemukan.');
            return redirect()->route('alat-test-booking-list.index');
        }

        return view('pages.admin.alat-test-booking-list.show', compact('booking'));
    }

    public function update($id, $value) { 
        try {
            DB::beginTransaction();
            
            $item = AlatTestBooking::with(['alatTestItemBooking.alatTestItem.alatTest', 'user'])->findOrFail($id); 
            
            // CEK APAKAH BOOKING SUDAH EXPIRED/BATAL/DIKEMBALIKAN
            if (in_array($item->status, ['EXPIRED', 'BATAL', 'DIKEMBALIKAN'])) {
                session()->flash('alert-failed', 'Tidak bisa mengupdate booking yang sudah ' . $item->status);
                return redirect()->route('alat-test-booking-list.index');
            }

            $user_name = $item->user->name; 
            $user_email = $item->user->email; 
            $admin_name = Auth::user()->name; 
            $admin_email = Auth::user()->email;

            // TENTUKAN STATUS BARU
            if ($value == 1) { 
                $data['status'] = 'DISETUJUI'; 
                
                // Cek apakah tanggal booking sudah lewat
                $bookingDate = Carbon::parse($item->date);
                $today = Carbon::today();
                
                if ($bookingDate->lt($today)) {
                    session()->flash('alert-failed', 'Tidak bisa menyetujui booking yang tanggalnya sudah lewat');
                    DB::rollBack();
                    return redirect()->route('alat-test-booking-list.index');
                }
                
                // Update status alat
                foreach ($item->alatTestItemBooking as $itemBooking) {
                    if ($itemBooking->alatTestItem) {
                        $itemBooking->alatTestItem->status = 'DIPINJAM';
                        $itemBooking->alatTestItem->save();
                    }
                }
                
            } elseif ($value == 2) { 
                $data['status'] = 'DIKEMBALIKAN'; 
                
                foreach ($item->alatTestItemBooking as $itemBooking) {
                    if ($itemBooking->alatTestItem) {
                        $itemBooking->alatTestItem->status = 'TERSEDIA';
                        $itemBooking->alatTestItem->save();
                    }
                }
                
            } elseif ($value == 0) { 
                $data['status'] = 'DITOLAK'; 
                
                if ($item->status == 'DISETUJUI') {
                    foreach ($item->alatTestItemBooking as $itemBooking) {
                        if ($itemBooking->alatTestItem && $itemBooking->alatTestItem->status == 'DIPINJAM') {
                            $itemBooking->alatTestItem->status = 'TERSEDIA';
                            $itemBooking->alatTestItem->save();
                        }
                    }
                }
                
            } else { 
                session()->flash('alert-failed', 'Perintah tidak dimengerti'); 
                return redirect()->route('alat-test-booking-list.index'); 
            }

            // UPDATE DATA
            if ($item->update($data)) { 
                session()->flash('alert-success', 'Booking Alat Test sekarang ' . $data['status']); 
                
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
                
                DB::commit();
                
            } else { 
                DB::rollBack();
                session()->flash('alert-failed', 'Booking Alat Test gagal diupdate'); 
            } 
            
            return redirect()->route('alat-test-booking-list.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating alat test booking: ' . $e->getMessage());
            session()->flash('alert-failed', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->route('alat-test-booking-list.index');
        }
    }
} 