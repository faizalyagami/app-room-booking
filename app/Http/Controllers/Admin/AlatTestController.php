<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatTestRequest;
use App\Models\AlatTest;
use App\Models\AlatTestInoutItem;
use App\Models\AlatTestItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AlatTestController extends Controller
{
    public function json()
    {
        $data = AlatTestItem::with([
                'alatTest'
            ])
            ->whereHas('alatTest', function($at) {
                $at->whereNull('deleted_at');
            })
            ->get();

        $result = [];

        foreach ($data as $index => $item) {
            $result[] = [
                'index' => $index + 1,
                'id' => $item->id,
                'alat_test_id'=> $item->alat_test_id,
                'name'=> $item->alatTest->name,
                'serial_number' => $item->serial_number,
                'type' => $item->type,
                'quantity' => $item->quantity,
                'status' => $item->status
            ];
        }

        return response()->json(['data' => $result]);
    }


    public function index()
    {
        return view('pages.admin.alat-test.index');
    }

    public function create()
    {
        $groups = AlatTest::orderBy('name')->pluck('name', 'id');
        $types = [1 => 'Satuan', 'Lembar'];

        return view('pages.admin.alat-test.create', compact(
            'groups', 'types'
        ));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'alat_test_id' => ['required'], 
            'serial_number' => ['required', 'unique:alat_test_items,serial_number'], 
            'type' => ['required', 'integer'],
            'quantity' => ['required_if:type,2'],
        ]);

        $message = new AlatTestItem();
        $message->alat_test_id = $request->alat_test_id;
        $message->serial_number = $request->serial_number;
        $message->type = $request->type;
        $message->quantity = $request->quantity ?? 1;
        $message->save();

        return redirect()->route('alat-test.index')->with('success', 'Alat test berhasil ditambahkan');
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
        $item = AlatTestItem::with([
                'alatTest' 
            ])
            ->findOrFail($id);

        $logs = AlatTestInoutItem::where('alat_test_item_id', $id)
            ->select('alat_test_in_outs.id', 'alat_test_in_outs.date', 'alat_test_in_outs.type', 'alat_test_in_out_items.quantity')
            ->join('alat_test_in_outs', 'alat_test_in_outs.id', 'alat_test_in_out_id')
            ->orderBy('date', 'desc')
            ->limit(11)
            ->get();

        return view('pages.admin.alat-test.show', compact(
            'item', 'logs'
        ));
    }

    public function destroy($id)
    {
        $item = AlatTestItem::findOrFail($id);

        // Hapus alat test
        $item->delete();

        return redirect()->route('alat-test.index')->with('success', 'Data alat test berhasil dihapus.');
    }

    public function logs($id)
    {
        $item = AlatTestItem::with([
                'alatTest' 
            ])
            ->findOrFail($id);
            
        $logs = AlatTestInoutItem::where('alat_test_item_id', $id)
            ->select('alat_test_in_outs.id', 'alat_test_in_outs.date', 'alat_test_in_outs.type', 'alat_test_in_out_items.quantity')
            ->join('alat_test_in_outs', 'alat_test_in_outs.id', 'alat_test_in_out_id')
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.admin.alat-test.logs', compact(
            'item', 'logs'
        ));
    }
}
