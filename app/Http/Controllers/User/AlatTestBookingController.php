<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AlatTestBookingRequest;
use App\Models\AlatTest;
use App\Models\BookingAlat;
use App\Models\User;
use Illuminate\Http\Request;

class AlatTestBookingController extends Controller
{
    public function json()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => [],
            ], 401);
        }

        $data = BookingAlat::with('alatTest')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $result = $data->map(function ($item, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'alat_test' => $item->alatTest->name ?? 'N/A',
                'date' => $item->date,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'purpose' => $item->purpose,
                'status' => $item->status,
            ];
        });

        return response()->json([
            'data' => $result
        ]);
    }

    public function index()
    {
        $bookings = BookingAlat::with('user', 'alatTest')->latest()->get();
        return view('pages.user.alat-test.index', compact('bookings'));
    }

    public function create()
    {
        $alatTests = AlatTest::all();
        return view('pages.user.alat-test.create', compact('alatTests'));
    }

    public function store(AlatTestBookingRequest $request)
    {
        BookingAlat::create([
            'alat_test_id' => $request->alat_test_id,
            'user_id' => auth()->id(), // ambil user dari auth
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        return redirect()->route('alat-test-booking.index')->with('success', 'Booking alat test berhasil disimpan.');
    }

    public function edit($id)
    {
        $booking = BookingAlat::findOrFail($id);
        $alatTests = AlatTest::all();
        return view('pages.user.alat-test.edit', compact('booking', 'alatTests'));
    }

    public function update(AlatTestBookingRequest $request, $id)
    {
        $booking = BookingAlat::findOrFail($id);
        $booking->update($request->validated());

        return redirect()->route('alat-test-booking.index')->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $booking = BookingAlat::findOrFail($id);
        $booking->delete();

        return redirect()->route('alat-test-booking.index')->with('success', 'Booking berhasil dihapus.');
    }
}
