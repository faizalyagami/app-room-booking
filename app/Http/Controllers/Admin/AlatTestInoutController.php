<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatTestRequest;
use App\Models\AlatTest;
use App\Models\AlatTestInout;
use App\Models\AlatTestInoutItem;
use App\Models\AlatTestItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class AlatTestInoutController extends Controller
{
    public function json()
    {
        $data = AlatTestInout::with([
                'alatTestInoutItems'
            ])
            ->withCount('alatTestInoutItems')
            ->whereHas('alatTestInoutItems', function($at) {
                $at->whereNull('deleted_at');
            })
            ->get();

        $result = [];

        foreach ($data as $index => $item) {
            $result[] = [
                'index' => $index + 1,
                'id' => $item->id,
                'date' => $item->date,
                'description'=> $item->description,
                'type'=> $item->type, 
                'count'=> $item->alat_test_inout_items_count
            ];
        }

        return response()->json(['data' => $result]);
    }


    public function index()
    {
        return view('pages.admin.alat-test.inout.index');
    }

    public function create()
    {
        $nowdate = Carbon::now();
        $types = ["Masuk" => "Masuk", "Keluar" => "Keluar"];
        $items = AlatTestItem::with(['alatTest'])->orderBy('serial_number')->get();

        return view('pages.admin.alat-test.inout.create', [
            'nowdate' => $nowdate, 
            'types' => $types,
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => ['required'], 
            'type' => ['required'],
            'items' => ['required'],
        ]);

        DB::transaction(function () use($request) {
            $message = new AlatTestInout();
            $message->date = $request->date;
            $message->type = $request->type;
            $message->description = $request->description ?? '';
            $message->save();
    
            foreach($request->items as $key => $val) {
                $item = new AlatTestInoutItem();
                $item->alat_test_in_out_id = $message->id;
                $item->alat_test_item_id = $val;
                $item->quantity = $request->amount[$key];
                $item->save();

                $check = AlatTestItem::where('id', $val)->first();
                if($check !== null) {
                    if ($request->type == "Masuk") {
                        $check->quantity += $request->amount[$key];
                    } else {
                        $n = $check->quantity - $request->amount[$key];
                        if($n < 0) {
                            $n = 0;
                        }
                        $check->quantity = $n;
                    }
                    $check->save();
                }
            }
        });

        return redirect()->route('alat-test.inout.index')->with('success', 'Alat test berhasil ditambahkan');
    }


    public function edit($id)
    {
        $item = AlatTestItem::whereId($id)->first();
        $groups = AlatTest::orderBy('name')->pluck('name', 'id');
        $types = [1 => 'Satuan', 'Lembar'];

        return view('pages.admin.alat-test.edit', compact(
            'groups', 'types', 'item'
        ));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'alat_test_id' => ['required'], 
            'serial_number' => ['required', 'unique:alat_test_items,serial_number,'. $id], 
            'type' => ['required', 'integer'],
            'quantity' => ['required_if:type,2'],
        ]);

        $message = AlatTestItem::whereId($id)->first();
        $message->alat_test_id = $request->alat_test_id;
        $message->serial_number = $request->serial_number;
        $message->type = $request->type;
        $message->quantity = $request->type == 1 ? 1 : $request->quantity;
        $message->save();

        return redirect()->route('alat-test.index')->with('success', 'Alat test berhasil diperbaharui');
    }

    public function show($id)
    {
        $item = AlatTestInout::with([
                'alatTestInoutItems'
            ])
            ->whereHas('alatTestInoutItems', function($at) {
                $at->whereNull('deleted_at');
            })
            ->findOrFail($id);

        return view('pages.admin.alat-test.inout.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = AlatTestItem::findOrFail($id);

        // Hapus alat test
        $item->delete();

        return redirect()->route('alat-test.index')->with('success', 'Data alat test berhasil dihapus.');
    }

    public function getTools(Request $request)
    {
        $tools = AlatTestItem::with(['alatTest'])
            ->where('type', 2)
            ->orderBy('serial_number')
            ->get();

        if(count($tools)) {
            return response()->json(['status' => 'success', 'message' => 'Alat Test ditemukan!.', 'data' => $tools], 200);
        }

        return response()->json(['status' =>'error', 'message' => 'Alat Test tidak ditemukan.', 'data' => null], 201);
    }
}
