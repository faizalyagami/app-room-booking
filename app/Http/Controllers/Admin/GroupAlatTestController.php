<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatTestRequest;
use App\Models\AlatTest;
use App\Models\AlatTestItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class GroupAlatTestController extends Controller
{
    public function list()
    {
        /**
         $data = AlatTest::withSum([
             'items' => function ($query) {
                 $query->where('status', 'tersedia');
             }
         ], 'quantity')->get();
         */

         //menghitung jumlah item tersedia
        $data = AlatTest::withCount([
        'items as items_sum_quantity' => function ($query) {
            $query->where('status', 'tersedia');
        }
        ])->get();

        $result = [];
        // dd($data);
        foreach ($data as $index => $item) {
            $result[] = [
                'index' => $index + 1,
                'id' => $item->id,
                'photo'=> $item->photo,
                'name' => $item->name,
                'description' => $item->description,
                'items_sum' => $item->items_sum_quantity
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function index()
    {
        return view('pages.admin.alat-test.group.index');
    }

    public function create()
    {
        return view('pages.admin.alat-test.group.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'unique:alat_tests,name'], 
        ]);

        // Cek jika ada file foto
        $filename = null;
        if ($request->hasFile('photo')) {
            $filename = $request->file('photo')->store('assets/image/alat-test', 'public');
        }

        // Cegah duplikasi alat test berdasarkan name
        $alatTest = AlatTest::firstOrCreate(
            ['name' => $request->name],
            ['description' => $request->description],
            ['photo' => $filename]
        );

        return redirect()->route('alat-test.group.index')->with('success', 'Group Alat test berhasil ditambahkan');
    }

    public function show($id)
    {
        $item = AlatTest::with([
                'items' => function($i) {
                    $i->withCount(['alatTestItemBookings as bookings' => function($b) {
                        $b->whereHas('alatTestBooking', function($c) {
                            $c->where('date', '>', Carbon::now());
                        });
                    }]);
                }
            ])
            ->findOrFail($id);

        return view('pages.admin.alat-test.group.show', compact(
            'item'
        ));
    }

    public function edit($id)
    {
        $item = AlatTest::with('items')->findOrFail($id);

        return view('pages.admin.alat-test.group.edit', compact(
            'item'
        ));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'unique:alat_tests,name, '. $id], 
        ]);

        $alatTest = AlatTest::with('items')->findOrFail($id);

        $filename = null;
        if ($request->hasFile('photo')) {
            $filename = $request->file('photo')->store('assets/image/alat-test', 'public');
        }

        if($alatTest !== null) {
            $alatTest->name = $request->name;
            $alatTest->description = $request->description;
            $alatTest->photo = $filename;
            $alatTest->save();
        }

        return redirect()->route('alat-test.group.index')->with('success', 'Group Alat test berhasil diperbaharui');
    }

    public function destroy($id)
    {
        $alatTest = AlatTest::findOrFail($id);

        if($alatTest !== null) {
            $alatTest->deleted_at = Carbon::now();
            $alatTest->save();
        }

        return redirect()->route('alat-test.group.index')->with('success', 'Group alat test berhasil dihapus.');
    }
}
