<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;

class RoomPlotController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('name')->get();
        return view('pages.admin.room-plot.index', compact('rooms'));
    }

    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        return view('pages.admin.room-plot.create', compact('rooms'));
    }

    // TAMBAHKAN METHOD STORE
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'plot_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
            'plot_description' => 'nullable|string',
            'plot_valid_from' => 'nullable|date',
            'plot_valid_until' => 'nullable|date|after_or_equal:plot_valid_from',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Hapus gambar lama jika ada
        if ($room->plot_image && Storage::exists('public/' . $room->plot_image)) {
            Storage::delete('public/' . $room->plot_image);
        }

        // Upload gambar baru
        $path = $request->file('plot_image')->store('room-plots', 'public');

        $room->plot_image = $path;
        $room->plot_description = $request->plot_description;
        $room->plot_valid_from = $request->plot_valid_from;
        $room->plot_valid_until = $request->plot_valid_until;
        $room->save();

        return redirect()->route('admin.room-plot.index')
            ->with('alert-success', 'Plot ruangan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return view('pages.admin.room-plot.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plot_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            'plot_description' => 'nullable|string',
            'plot_valid_from' => 'nullable|date',
            'plot_valid_until' => 'nullable|date|after_or_equal:plot_valid_from',
        ]);

        $room = Room::findOrFail($id);

        if ($request->hasFile('plot_image')) {
            // Hapus gambar lama jika ada
            if ($room->plot_image && Storage::exists('public/' . $room->plot_image)) {
                Storage::delete('public/' . $room->plot_image);
            }

            $path = $request->file('plot_image')->store('room-plots', 'public');
            $room->plot_image = $path;
        }

        $room->plot_description = $request->plot_description;
        $room->plot_valid_from = $request->plot_valid_from;
        $room->plot_valid_until = $request->plot_valid_until;
        $room->save();

        return redirect()->route('admin.room-plot.index')
            ->with('alert-success', 'Plot ruangan berhasil diupdate');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        if ($room->plot_image && Storage::exists('public/' . $room->plot_image)) {
            Storage::delete('public/' . $room->plot_image);
        }

        $room->plot_image = null;
        $room->plot_description = null;
        $room->plot_valid_from = null;
        $room->plot_valid_until = null;
        $room->save();

        return response()->json(['success' => true]);
    }

    public function json()
    {
        $rooms = Room::select('id', 'name', 'plot_image', 'plot_description', 'plot_valid_from', 'plot_valid_until')
            ->orderBy('name')
            ->get();

        return DataTables::of($rooms)
            ->addIndexColumn()
            ->addColumn('plot_image_preview', function ($row) {
                if ($row->plot_image) {
                    return '<img src="' . asset('storage/' . $row->plot_image) . '" style="width: 100px; height: auto;">';
                }
                return '<span class="badge badge-secondary">Belum ada plot</span>';
            })
            ->addColumn('validity', function ($row) {
                $from = $row->plot_valid_from ? date('d/m/Y', strtotime($row->plot_valid_from)) : '-';
                $until = $row->plot_valid_until ? date('d/m/Y', strtotime($row->plot_valid_until)) : '-';
                return $from . ' s/d ' . $until;
            })
            ->addColumn('status', function ($row) {
                if (!$row->plot_image) {
                    return '<span class="badge badge-secondary">Belum Diplot</span>';
                }

                $isValid = $row->isPlotValid();
                if ($isValid) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-danger">Kadaluarsa</span>';
                }
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.room-plot.edit', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit Plot
                    </a>
                    <button onclick="deletePlot(' . $row->id . ')" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                ';
            })
            ->rawColumns(['plot_image_preview', 'status', 'action'])
            ->make(true);
    }
}
