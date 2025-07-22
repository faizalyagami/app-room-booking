<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatTestRequest;
use App\Models\AlatTest;
use App\Models\AlatTestItem;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;


class AlatTestController extends Controller
{
    public function json()
    {
        // $data = AlatTest::all();
        // dd($data);
        $data = AlatTest::withCount([
            'items as stock' => function ($query) {
                $query->where('status', 'tersedia');
            }
        ])->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function index()
    {
        return view('pages.admin.alat-test.index');
    }

    public function create()
    {
        return view('pages.admin.alat-test.edit_or_create');
    }

    public function store(AlatTestRequest $request)
    {
        $data = $request->only(['name', 'description']);

        // Cek jika ada file foto
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('assets/image/alat-test', 'public');
        }

        // Cegah duplikasi alat test berdasarkan name
        $alatTest = AlatTest::firstOrCreate(
            ['name' => $data['name']],
            $data
        );

        // Cek apakah alat test baru dibuat atau sudah ada
        if ($alatTest->wasRecentlyCreated) {
            // Jika baru dibuat, buat serial number sesuai stok
            if (is_numeric($request->stock) && $request->stock > 0) {
                $tahun = now()->year;
                $nama = strtoupper(str_replace(' ', '-', $alatTest->name));

                for ($i = 1; $i <= $request->stock; $i++) {
                    $nomorUrut = str_pad($i, 3, '0', STR_PAD_LEFT);
                    $serialNumber = "LabPsi/{$nama}/{$nomorUrut}/{$tahun}";

                    AlatTestItem::create([
                        'alat_test_id' => $alatTest->id,
                        'serial_number' => $serialNumber,
                        'status' => 'tersedia'
                    ]);
                }
            }
        }

        return redirect()->route('alat-test-admin.index')->with('success', 'Alat test berhasil ditambahkan');
    }


    public function edit($id)
    {
        $item = AlatTest::with('items')->findOrFail($id);
        $serialNumbers = $item->items->pluck('serial_number')->implode(PHP_EOL);
        return view('pages.admin.alat-test.edit_or_create', compact('item', 'serialNumbers'));
    }

    public function update(AlatTestRequest $request, $id)
    {
        $alatTest = AlatTest::with('items')->findOrFail($id);
        $data = $request->only(['name', 'description']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('assets/image/alat-test', 'public');
        }

        $alatTest->update($data);

        // Hitung jumlah stok saat ini
        $stokSaatIni = $alatTest->items()->count();

        // Jika jumlah stock baru > stok saat ini, tambahkan item baru
        if (is_numeric($request->stock) && $request->stock > $stokSaatIni) {
            $tahun = now()->year;
            $nama = strtoupper(str_replace(' ', '-', $alatTest->name));
            $jumlahTambahan = $request->stock - $stokSaatIni;

            for ($i = 1; $i <= $jumlahTambahan; $i++) {
                $nomorUrut = str_pad($stokSaatIni + $i, 3, '0', STR_PAD_LEFT);
                $serialNumber = "LabPsi/{$nama}/{$nomorUrut}/{$tahun}";

                AlatTestItem::create([
                    'alat_test_id' => $alatTest->id,
                    'serial_number' => $serialNumber,
                    'status' => 'tersedia'
                ]);
            }
        }

        return redirect()->route('alat-test-admin.index')->with('success', 'Alat test berhasil diperbaharui');
    }

    public function show($id)
    {
        $item = AlatTest::with('items')->findOrFail($id);
        return view('pages.admin.alat-test.show', compact('item'));
    }

    public function destroy($id)
    {
        $alatTest = AlatTest::findOrFail($id);

        // Hapus semua item terkait jika diperlukan
        $alatTest->items()->delete(); // jika relasi items()

        // Hapus file foto dari storage (opsional)
        if ($alatTest->photo && Storage::disk('public')->exists($alatTest->photo)) {
            Storage::disk('public')->delete($alatTest->photo);
        }

        // Hapus alat test
        $alatTest->delete();

        return redirect()->route('alat-test-admin.index')->with('success', 'Data alat test berhasil dihapus.');
    }
}
