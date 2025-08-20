<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AlatTestItem;
use App\Models\AlatTestItemBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlatTestListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.user.alat-test.index');
    }

    public function json(Request $request)
    {
        $columns = ['id', 'photo', 'name', 'description', 'capacity'];

        $query = AlatTestItem::query();
        $query->with(['alatTest']);
        $query->join('alat_tests', 'alat_tests.id', 'alat_test_items.alat_test_id');

        // Searching
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Ordering
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';
        $orderDir = $request->input('order.0.dir') ?? 'asc';
        $query->orderBy($orderColumn, $orderDir);

        $query->withCount(['alatTestItemBookings as bookings' => function($b) {
            $b->whereHas('alatTestBooking', function($c) {
                $c->where('date', '>', Carbon::now());
            });
        }]);

        $total = AlatTestItem::count();
        $filtered = $query->count();

        // Paging
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $tools = $query->skip($start)->take($length)->get();

        // Data format
        $data = [];
        foreach ($tools as $index => $tool) {
            $data[] = [
                'id' => $tool->id, 
                'DT_RowIndex' => $start + $index + 1,
                'photo' => $tool->alatTest->photo,
                'name' => $tool->alatTest->name,
                'description' => $tool->alatTest->description,
                'serial_number' => $tool->serial_number,
                'bookings' => $tool->bookings,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function getTools(Request $request)
    {
        $bookings = AlatTestItemBooking::whereHas('alatTestBooking', function($ab) use($request) {
                $ab->where('date', $request->date)
                ->whereRaw('((start_time <= "'. $request->start .'" and end_time > "'. $request->start .'") or (start_time <= "'. $request->end .'" and end_time > "'. $request->end .'" ) or (end_time > "'. $request->start .'" and end_time < "'. $request->end .'"))')
                    // ->whereRaw('"'. $request->start .'" between start_time and end_time')
                    // `date` = "2025-08-08" and ((start_time <= "10:00" and end_time > "10:00") or (start_time <= "15:00" and end_time > "15:00" ) or (end_time > "10:00" and end_time <"15:00"))
                    ->where('status', 'DISETUJUI');
            })
            ->pluck('alat_test_item_id');

        $tools = AlatTestItem::with(['alatTest'])
            ->orderBy('serial_number')
            ->get();

        if(count($tools)) {
            return response()->json(['status' => 'success', 'message' => 'Alat Test ditemukan!.', 'data' => ['tools' => $tools, 'bookings' => $bookings]], 200);
        }

        return response()->json(['status' =>'error', 'message' => 'Alat Test tidak ditemukan.', 'data' => null], 201);
    }
}
