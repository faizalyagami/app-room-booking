<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatTestRequest;
use App\Models\AlatTest;
use App\Models\AlatTestItem;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('assets/image/alat-test', 'public');
        }

        $alatTest = AlatTest::create($data);

        //membuat serial number otomatis
        $tahun = now()->year;
        $nama = strtoupper(str_replace(' ', '-', $alatTest->name));

        //hitung jumlah alat test dengan nama yang sama
        $jumlahSama = AlatTestItem::whereHas('alatTest', function ($q) use ($alatTest) {
            $q->where('name', $alatTest->name);
        });

        //buat nomer urut 3 digit
        $nomorUrut = str_pad($jumlahSama + 1, 3, '0', STR_PAD_LEFT);

        $serialNumber = "LabPsi/{$nama}/{$nomorUrut}/{$tahun}";

        if ($request->has('serial_number')) {
            AlatTestItem::create([
                'alat_test_id' => $alatTest->id,
                'serial_number' => $serialNumber,
                // 'serial_number' => $request->serial_number,
                'status' => 'tersedia'
            ]);
        }
        return redirect()->route('alat-test.index')->with('success', 'Alat test berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = AlatTest::with('items')->findOrFail($id);
        $serialNumbers = $item->items->pluck('serial_number')->implode(PHP_EOL);
        return view('pages.admin.alat-test.edit_or_create', compact('item', 'serialNumbers'));
    }
}
