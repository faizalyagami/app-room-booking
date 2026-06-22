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
            <form method="GET" action="{{ route('admin.calendar.index') }}" class="row">
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
                            <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-undo"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- LEGENDA WARNA --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h6 class="font-weight-bold mb-3"><i class="fas fa-info-circle"></i> Keterangan Warna:</h6>
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#28a745;"></span>
                            <span>Booking oleh Mahasiswa</span>
                        </div>

                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#dc3545;"></span>
                            <span>Booking oleh Laboratorium</span>
                        </div>

                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#17a2b8;"></span>
                            <span>Booking yang Disetujui</span>
                        </div>

                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#ffc107;"></span>
                            <span>Booking dalam Proses (Pending)</span>
                        </div>

                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#6c757d;"></span>
                            <span>Booking Selesai/Expired</span>
                        </div>

                        <div class="mr-4 mb-2 d-flex align-items-center">
                            <span class="legend-color" style="background-color:#007bff;"></span>
                            <span>Tanggal Hari Ini</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kalender --}}
    <div class="card">
        <div class="card-header">
            <h4>{{ $startDate->translatedFormat('F') }} {{ $year }}</h4>
            <div class="card-header-action">
                <a href="{{ route('admin.calendar.index', ['month' => $month - 1, 'year' => $year, 'room_id' => $roomId]) }}"
                    class="btn btn-sm btn-primary">
                    <i class="fas fa-chevron-left"></i> Bulan Sebelumnya
                </a>
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-calendar-day"></i> Hari Ini
                </a>
                <a href="{{ route('admin.calendar.index', ['month' => $month + 1, 'year' => $year, 'room_id' => $roomId]) }}"
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
                            <th style="width: 14.28%;">Senin</th>
                            <th style="width: 14.28%;">Selasa</th>
                            <th style="width: 14.28%;">Rabu</th>
                            <th style="width: 14.28%;">Kamis</th>
                            <th style="width: 14.28%;">Jumat</th>
                            <th style="width: 14.28%;">Sabtu</th>
                            <th style="width: 14.28%;">Minggu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $firstDayOfMonth = Carbon\Carbon::create($year, $month, 1);
                            $startOfWeek = $firstDayOfMonth->copy()->startOfWeek(Carbon\Carbon::MONDAY);
                            $endOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                            $currentDay = $startOfWeek->copy();
                            $loopEnd = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::MONDAY);
                        @endphp

                        @while ($currentDay <= $loopEnd)
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $dateKey = $currentDay->toDateString();
                                        $dayData = $calendarData[$dateKey] ?? null;
                                        $isCurrentMonth = $dayData && $dayData['isCurrentMonth'] ?? false;
                                        $isToday = $dayData && $dayData['isToday'] ?? false;
                                        $bookings = $dayData ? $dayData['bookings'] : collect();
                                    @endphp
                                    <td class="{{ $isToday ? 'bg-primary text-white' : '' }} 
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
                                                    @php
                                                        $bgColor = '#28a745';
                                                        $statusLabel = 'Disetujui';

                                                        switch ($booking->status) {
                                                            case 'BOOKING_BY_LAB':
                                                                $bgColor = '#dc3545';
                                                                $statusLabel = 'Booking Lab';
                                                                break;
                                                            case 'DISETUJUI':
                                                                $bgColor = '#28a745';
                                                                $statusLabel = 'Disetujui';
                                                                break;
                                                            case 'DIGUNAKAN':
                                                                $bgColor = '#17a2b8';
                                                                $statusLabel = 'Sedang Digunakan';
                                                                break;
                                                            case 'PENDING':
                                                                $bgColor = '#ffc107';
                                                                $statusLabel = 'Pending';
                                                                break;
                                                            case 'SELESAI':
                                                            case 'EXPIRED':
                                                                $bgColor = '#6c757d';
                                                                $statusLabel = 'Selesai/Expired';
                                                                break;
                                                            case 'BATAL':
                                                            case 'DITOLAK':
                                                                $bgColor = '#6c757d';
                                                                $statusLabel = 'Batal/Ditolak';
                                                                break;
                                                            default:
                                                                $bgColor = '#6c757d';
                                                                $statusLabel = $booking->status;
                                                        }
                                                    @endphp
                                                    <div class="calendar-event"
                                                        style="font-size: 10px; 
                                                                background-color: {{ $bgColor }};
                                                                color: white;
                                                                padding: 2px 4px;
                                                                border-radius: 3px;
                                                                margin-bottom: 2px;
                                                                cursor: pointer;"
                                                        onclick="showBookingDetail({{ $booking->id }})"
                                                        title="{{ $booking->room->name }} - {{ $statusLabel }}">
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
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailModalLabel">
                        <i class="fas fa-info-circle"></i> Detail Peminjaman
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="bookingDetailBody">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
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
            transition: opacity 0.2s;
        }

        .calendar-event:hover {
            opacity: 0.85;
            transform: scale(1.02);
        }

        .legend-color {
            display: inline-block;
            width: 30px;
            height: 20px;
            border-radius: 3px;
            margin-right: 8px;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        /* Modal fix */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1060 !important;
        }

        .modal-content {
            z-index: 1070 !important;
        }

        /* Prevent body scroll when modal open */
        .modal-open {
            overflow: hidden !important;
        }

        .modal-open .modal {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
    </style>
@endpush

@push('after-script')
    <script>
        function showBookingDetail(id) {
            if ($('#bookingDetailModal').hasClass('show')) {
                updateBookingDetail(id);
                return;
            }

            $('#bookingDetailModal').modal({
                backdrop: false,
                keyboard: true,
                show: true
            });

            updateBookingDetail(id);
        }

        function updateBookingDetail(id) {
            $('#bookingDetailBody').html(`
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('admin.calendar.booking.detail', ':id') }}'.replace(':id', id),
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(data) {
                    let badgeColor = 'secondary';
                    let statusText = data.status || '-';

                    switch (data.status) {
                        case 'BOOKING_BY_LAB':
                            badgeColor = 'danger';
                            statusText = 'Booking Lab';
                            break;
                        case 'DISETUJUI':
                            badgeColor = 'success';
                            statusText = 'Disetujui';
                            break;
                        case 'DIGUNAKAN':
                            badgeColor = 'info';
                            statusText = 'Sedang Digunakan';
                            break;
                        case 'PENDING':
                            badgeColor = 'warning';
                            statusText = 'Pending';
                            break;
                        case 'SELESAI':
                            badgeColor = 'secondary';
                            statusText = 'Selesai';
                            break;
                        case 'EXPIRED':
                            badgeColor = 'secondary';
                            statusText = 'Expired';
                            break;
                        case 'BATAL':
                            badgeColor = 'secondary';
                            statusText = 'Batal';
                            break;
                        case 'DITOLAK':
                            badgeColor = 'secondary';
                            statusText = 'Ditolak';
                            break;
                        default:
                            badgeColor = 'secondary';
                            statusText = data.status || '-';
                    }

                    let html = `
                        <table class="table table-striped table-sm">
                            <tr>
                                <th style="width: 35%;">Ruangan</th>
                                <td>${data.room ? data.room.name : 'Data tidak tersedia'}</td>
                            </tr>
                            <tr>
                                <th>Peminjam</th>
                                <td>${data.user ? data.user.name : 'Data tidak tersedia'}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>${data.date ? new Date(data.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}</td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td>${data.start_time ? data.start_time.substring(0,5) : '-'} - ${data.end_time ? data.end_time.substring(0,5) : '-'}</td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td>${data.purpose || '-'}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span class="badge badge-${badgeColor}">${statusText}</span></td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>${data.created_at ? new Date(data.created_at).toLocaleString('id-ID') : '-'}</td>
                            </tr>
                        </table>
                    `;
                    $('#bookingDetailBody').html(html);
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Gagal memuat detail booking.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMsg = 'Request timeout. Silakan coba lagi.';
                    }

                    $('#bookingDetailBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> ${errorMsg}
                            <br><small>Silakan refresh halaman dan coba lagi.</small>
                            <br><br>
                            <button class="btn btn-sm btn-primary" onclick="updateBookingDetail(${id})">
                                <i class="fas fa-redo"></i> Coba Lagi
                            </button>
                        </div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            $('#bookingDetailModal').on('shown.bs.modal', function() {
                $('.modal-backdrop').not(':last').remove();
                $('body').addClass('modal-open');
            });

            $('#bookingDetailModal').on('hidden.bs.modal', function() {
                $('#bookingDetailBody').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                `);

                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                $('body').css('overflow', '');
                $(document).off('scroll');
            });

            $('#bookingDetailModal .close, #bookingDetailModal .btn-secondary').on('click', function() {
                $('#bookingDetailModal').modal('hide');
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#bookingDetailModal').hasClass('show')) {
                    $('#bookingDetailModal').modal('hide');
                }
            });
        });

        $(document).on('hidden.bs.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });

        $(document).on('click', '.modal-backdrop', function() {
            $('.modal.show').modal('hide');
        });
    </script>
@endpush
