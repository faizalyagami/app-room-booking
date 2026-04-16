<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;

class InfoImageController extends Controller
{
    public function index()
    {
        return view('pages.admin.info-image.index');
    }

    public function create()
    {
        return view('pages.admin.info-image.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
            'description' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'sort_order' => 'nullable|integer'
        ]);

        $path = $request->file('image')->store('info-images', 'public');

        InfoImage::create([
            'title' => $request->title,
            'image' => $path,
            'description' => $request->description,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'is_active' => true,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.info-image.index')
            ->with('alert-success', 'Gambar info berhasil ditambahkan');
    }

    public function edit($id)
    {
        $infoImage = InfoImage::findOrFail($id);
        return view('pages.admin.info-image.edit', compact('infoImage'));
    }

    public function update(Request $request, $id)
    {
        $infoImage = InfoImage::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            'description' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($infoImage->image && Storage::exists('public/' . $infoImage->image)) {
                Storage::delete('public/' . $infoImage->image);
            }
            $path = $request->file('image')->store('info-images', 'public');
            $infoImage->image = $path;
        }

        $infoImage->title = $request->title;
        $infoImage->description = $request->description;
        $infoImage->valid_from = $request->valid_from;
        $infoImage->valid_until = $request->valid_until;
        $infoImage->is_active = $request->has('is_active');
        $infoImage->sort_order = $request->sort_order ?? 0;
        $infoImage->save();

        return redirect()->route('admin.info-image.index')
            ->with('alert-success', 'Gambar info berhasil diupdate');
    }

    public function destroy($id)
    {
        $infoImage = InfoImage::findOrFail($id);

        if ($infoImage->image && Storage::exists('public/' . $infoImage->image)) {
            Storage::delete('public/' . $infoImage->image);
        }

        $infoImage->delete();

        return response()->json(['success' => true]);
    }

    public function json()
    {
        $data = InfoImage::orderBy('sort_order')->orderBy('created_at', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('image_preview', function ($row) {
                return '<img src="' . asset('storage/' . $row->image) . '" style="width: 100px; height: auto;">';
            })
            ->addColumn('validity', function ($row) {
                $from = $row->valid_from ? date('d/m/Y', strtotime($row->valid_from)) : '-';
                $until = $row->valid_until ? date('d/m/Y', strtotime($row->valid_until)) : '-';
                return $from . ' s/d ' . $until;
            })
            ->addColumn('status', function ($row) {
                if (!$row->is_active) {
                    return '<span class="badge badge-secondary">Nonaktif</span>';
                }
                $isValid = $row->isValid();
                if ($isValid) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-danger">Kadaluarsa</span>';
                }
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.info-image.edit', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button onclick="deleteImage(' . $row->id . ')" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                ';
            })
            ->rawColumns(['image_preview', 'status', 'action'])
            ->make(true);
    }

    public function toggleStatus($id)
    {
        $infoImage = InfoImage::findOrFail($id);
        $infoImage->is_active = !$infoImage->is_active;
        $infoImage->save();

        return response()->json(['success' => true, 'is_active' => $infoImage->is_active]);
    }
}
