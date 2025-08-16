<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingList;
use Illuminate\Http\Request;
use App\Models\Room;
use Carbon\Carbon;

class RoomListController extends Controller
{
    public function index()
    {
        return view('pages.user.room.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Room::findOrFail($id);
        $bookings = BookingList::where('room_id', $id)
            ->where('date', '>', Carbon::now())
            ->get();

        return view('pages.user.room.show', [
            'item'  => $item, 
            'bookings' => $bookings
        ]);
    }

    public function json(Request $request)
    {
        $columns = ['id', 'photo', 'name', 'description', 'capacity'];

        $query = Room::query();

        // Searching
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('capacity', 'like', "%{$search}%");
            });
        }

        // Ordering
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';
        $orderDir = $request->input('order.0.dir') ?? 'asc';
        $query->orderBy($orderColumn, $orderDir);

        $query->withCount(['bookings' => function($b) {
            $b->where('date', '>', Carbon::now());
        }]);

        $total = Room::count();
        $filtered = $query->count();

        // Paging
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $rooms = $query->skip($start)->take($length)->get();

        // Data format
        $data = [];
        foreach ($rooms as $index => $room) {
            $data[] = [
                'id' => $room->id, 
                'DT_RowIndex' => $start + $index + 1,
                'photo' => $room->photo,
                'name' => $room->name,
                'description' => $room->description,
                'capacity' => $room->capacity,
                'booking' => $room->bookings_count,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }
}
