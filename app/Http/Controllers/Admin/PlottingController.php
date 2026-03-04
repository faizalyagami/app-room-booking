<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PlottingTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PlottingImport;
use App\Models\BookingList;
use App\Models\Plotting;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PlottingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plottings = Plotting::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pages.admin.plotting.index', compact('plottings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        $days = [
            '1' => 'Senin',
            '2' => 'Selasa', 
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
            '6' => 'Sabtu',
            '7' => 'Minggu'
        ];

        return view('pages.admin.plotting.create', compact('rooms', 'days'));
    }

    public function show($id)
    {
        // Redirect ke preview
        return redirect()->route('plotting.preview', $id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        DB::beginTransaction();
        try {
            // Nonaktifkan plotting active sebelumnya
            Plotting::where('is_active', true)->update(['is_active' => false]);

            // Buat plotting baru
            $plotting = Plotting::create([
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => true,
                'created_by' => Auth::id()
            ]);

            // Hapus booking lab yang existing untuk range tanggal ini
            BookingList::where('status', 'BOOKING_BY_LAB')
                ->whereBetween('date', [$request->tanggal_mulai, $request->tanggal_selesai])
                ->delete();
            
            DB::commit();

            return redirect()->route('plotting.import', $plotting->id)
                ->with('success', 'Plotting berhasil dibuat. Silahkan upload file excel.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat plotting: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function importForm($id)
    {
        $plotting = Plotting::findOrFail($id);
        return view('pages.admin.plotting.import', compact('plotting'));
    }

    /**
     * Process import Excel
     */
    public function importExcel(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        $plotting = Plotting::findOrFail($id);
        $labUser = User::where('role', 'ADMIN')->first();

        if (!$labUser) {
            return back()->with('error', 'User admin tidak ditemukan');
        }

        DB::beginTransaction();

        try {
            // HAPUS SEMUA DATA LAMA UNTUK PERIODE INI
            BookingList::where('status', 'BOOKING_BY_LAB')
                ->whereBetween('date', [$plotting->tanggal_mulai, $plotting->tanggal_selesai])
                ->delete();

            $import = new PlottingImport($plotting, $labUser->id);
            Excel::import($import, $request->file('file'));

            // Cek apakah ada errors
            $errors = $import->getErrors();
            if (!empty($errors)) {
                DB::rollBack();
                
                $errorMessages = implode('<br>', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessages .= '<br>... dan ' . (count($errors) - 10) . ' error lainnya';
                }
                
                return back()->with('error', 'Gagal import:<br>' . $errorMessages);
            }

            $plotting->update([
                'data_plotting' => $import->getPlottingData()
            ]);

            DB::commit();

            $rowCount = $import->getRowCount();
            return redirect()->route('plotting.index')
                ->with('success', 'Berhasil import ' . $rowCount . ' jadwal plotting (data lama dihapus)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(new PlottingTemplateExport, 'template_plotting_ruangan.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plotting = Plotting::findOrFail($id);

        DB::beginTransaction();

        try {
            if ($plotting->is_active) {
                BookingList::where('status', 'BOOKING_BY_LAB')
                    ->whereBetween('date', [$plotting->tanggal_mulai, $plotting->tanggal_selesai])
                    ->delete();
            }

            $plotting->delete();
            
            DB::commit();
            
            return redirect()->route('plotting.index')
                ->with('success', 'Plotting berhasil dihapus');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Activate plotting
     */
    public function activate($id)
    {
        DB::beginTransaction();
        try {
            // Nonaktifkan semua
            Plotting::where('is_active', true)->update(['is_active' => false]);
            
            // Aktifkan yang dipilih
            $plotting = Plotting::findOrFail($id);
            $plotting->update(['is_active' => true]);

            DB::commit();

            // PERBAIKAN 3: dari 'pages.admin.plotting.index' ke 'plotting.index'
            return redirect()->route('plotting.index')
                ->with('success', 'Plotting berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengaktifkan: ' . $e->getMessage());
        }
    }

    /**
     * Preview plotting
     */
    public function preview($id)
    {
        $plotting = Plotting::with('creator')->findOrFail($id);
        
        // Ambil booking untuk plotting ini
        $bookings = BookingList::with(['room', 'user'])
            ->where('status', 'BOOKING_BY_LAB')
            ->whereBetween('date', [$plotting->tanggal_mulai, $plotting->tanggal_selesai])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date');

        return view('pages.admin.plotting.preview', compact('plotting', 'bookings'));
    }
}