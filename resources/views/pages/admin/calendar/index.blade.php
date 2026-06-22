@extends('layouts.main')

@section('title', 'Kalender Peminjaman Ruangan - ROOMING')

@section('header-title', 'Kalender Peminjaman Ruangan')

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Kalender</a></div>
    <div class="breadcrumb-item active">Kalender Ruangan</div>
@endsection

@section('section-title', 'Kalender Peminjaman Ruangan')
@section('section-lead', 'Lihat jadwal peminjaman ruangan per tanggal.')

@section('content')

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.calendar.index') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bulan</label>
                        <select name="month" class="form-control">
                            @foreach ($months as $key => $monthName)
                                <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>
                                    {{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="year" class="form-control">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter Ruangan</label>
                        <select name="room_id" class="form-control">
                            <option value="">Semua Ruangan</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <a href="{{ route('user.calendar.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-undo"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Kalender --}}
    <div class="card">
        <div class="card-header">
            <h4>{{ $startDate->translatedFormat('F') }} {{ $year }}</h4>
            <div class="card-header-action">
                <a href="{{ route('user.calendar.index', ['month' => $month - 1, 'year' => $year, 'room_id' => $roomId]) }}"
                    class="btn btn-sm btn-primary">
                    <i class="fas fa-chevron-left"></i> Bulan Sebelumnya
                </a>
                <a href="{{ route('user.calendar.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-calendar-day"></i> Hari Ini
                </a>
                <a href="{{ route('user.calendar.index', ['month' => $month + 1, 'year' => $year, 'room_id' => $roomId]) }}"
                    class="btn btn-sm btn-primary">
                    Bulan Berikutnya <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead>
                        <tr>
                            <th style="width: 14.28%;">Minggu</th>
                            <th style="width: 14.28%;">Senin</th>
                            <th style="width: 14.28%;">Selasa</th>
                            <th style="width: 14.28%;">Rabu</th>
                            <th style="width: 14.28%;">Kamis</th>
                            <th style="width: 14.28%;">Jumat</th>
                            <th style="width: 14.28%;">Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $firstDayOfMonth = Carbon\Carbon::create($year, $month, 1);
                            $startOfWeek = $firstDayOfMonth->copy()->startOfWeek();
                            $endOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                            $currentDay = $startOfWeek->copy();
                        @endphp

                        @while ($currentDay <= $endOfMonth)
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $dateKey = $currentDay->toDateString();
                                        $dayData = $calendarData[$dateKey] ?? null;
                                        $isCurrentMonth = $dayData && $dayData['date']->month == $month;
                                        $isToday = $dayData && $dayData['date']->isToday();
                                        $isWeekend = $dayData && $dayData['date']->isWeekend();
                                        $bookings = $dayData ? $dayData['bookings'] : collect();
                                    @endphp
                                    <td class="{{ $isToday ? 'bg-primary text-white' : ($isWeekend ? 'bg-light' : '') }} 
                           {{ !$isCurrentMonth ? 'text-muted' : '' }}"
                                        style="vertical-align: top; height: 100px; width: 14.28%;">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $currentDay->day }}</strong>
                                            @if ($isToday)
                                                <span class="badge badge-danger">hari ini</span>
                                            @endif
                                        </div>
                                        @if ($isCurrentMonth && $bookings->count() > 0)
                                            <div class="mt-1">
                                                @foreach ($bookings as $booking)
                                                    <div class="calendar-event {{ $booking->status == 'BOOKING_BY_LAB' ? 'event-lab' : 'event-user' }}"
                                                        style="font-size: 10px; 
                                    background-color: {{ $booking->status == 'BOOKING_BY_LAB' ? '#dc3545' : '#28a745' }};
                                    color: white;
                                    padding: 2px 4px;
                                    border-radius: 3px;
                                    margin-bottom: 2px;
                                    cursor: pointer;"
                                                        onclick="showBookingDetail({{ $booking->id }})">
                                                        <strong>{{ $booking->room->name }}</strong>
                                                        <span>{{ substr($booking->start_time, 0, 5) }}-{{ substr($booking->end_time, 0, 5) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    @php
                                        $currentDay->addDay();
                                    @endphp
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail Booking --}}
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Peminjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="bookingDetailBody">
                    <p>Memuat data...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-style')
    <style>
        .calendar-table td {
            vertical-align: top;
            height: 100px;
            padding: 5px;
        }

        .calendar-event {
            font-size: 10px;
            padding: 2px 4px;
            border-radius: 3px;
            margin-bottom: 2px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-event:hover {
            opacity: 0.8;
        }

        .event-lab {
            background-color: #dc3545 !important;
        }

        .event-user {
            background-color: #28a745 !important;
        }
    </style>
@endpush

@push('after-script')
    <script>
        function showBookingDetail(id) {
            $('#bookingDetailModal').modal('show');
            $('#bookingDetailBody').html('<p>Memuat data...</p>');

            $.ajax({
                url: '/calendar/booking/' + id,
                type: 'GET',
                success: function(data) {
                    let html = `
          <table class="table table-striped">
            <tr><th>Ruangan</th><td>${data.room.name}</td></tr>
            <tr><th>Peminjam</th><td>${data.user.name}</td></tr>
            <tr><th>Tanggal</th><td>${data.date}</td></tr>
            <tr><th>Waktu</th><td>${data.start_time.substring(0,5)} - ${data.end_time.substring(0,5)}</td></tr>
            <tr><th>Keperluan</th><td>${data.purpose}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-${data.status === 'BOOKING_BY_LAB' ? 'danger' : 'success'}">${data.status}</span></td></tr>
          </table>
        `;
                    $('#bookingDetailBody').html(html);
                },
                error: function() {
                    $('#bookingDetailBody').html('<p class="text-danger">Gagal memuat detail booking.</p>');
                }
            });
        }
    </script>
@endpush
