@extends('layouts.main')

@section('title')
    Buat Booking - ROOMING
@endsection

@section('header-title')
    Buat Booking
@endsection

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
    <div class="breadcrumb-item"><a href="{{ route('my-booking-list.index') }}">My Booking</a></div>
    <div class="breadcrumb-item active">
        Buat Booking
    </div>
@endsection

@section('section-title')
    Buat Booking
@endsection

@section('section-lead')
    Silakan isi form di bawah ini untuk membuat booking.
@endsection

@section('content')
    <div class="row">
        <div class="col ">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('my-booking-list.store') }}" method="post" name="form-booking" id="form-booking">
                        @csrf

                        <div class="form-group">
                            <label for="room_id">Nama Ruangan</label>
                            <select name="room_id" class="form-control" id="room_id" required>
                                <option value="">Pilih Ruangan</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}</option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date">Tanggal Booking</label>
                            <input type="text" name="date" class="form-control datepicker"
                                data-min-date="{{ $nowdate }}" id="date" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- WAKTU BOOKING CUSTOM --}}
                        <div class="form-group">
                            <label>Waktu Booking</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="start_time" class="small">Jam Mulai</label>
                                    <input type="time" name="start_time" id="start_time" class="form-control"
                                        value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="end_time" class="small">Jam Selesai</label>
                                    <input type="time" name="end_time" id="end_time" class="form-control"
                                        value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="text-muted">Format waktu 24 jam (HH:MM). Contoh: 08:00, 13:30</small>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Keperluan</label>
                            <textarea name="purpose" class="form-control" id="purpose" style="height: 185px;" required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('my-booking-list.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    {{-- datepicker  --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.css">
    {{-- datepicker  --}}
    <style>
        input[type="time"]::-webkit-calendar-picker-indicator {
            background: #007bff;
            padding: 5px;
            border-radius: 3px;
            color: white;
            cursor: pointer;
        }

        input[type="time"] {
            padding: 10px 12px;
            border-radius: 4px;
        }
    </style>
@endpush

@push('after-script')
    {{-- datepicker  --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.js"></script>
    {{-- datepicker  --}}

    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            // Load bookings untuk cek overlap ketika ruangan atau tanggal berubah
            if ($("#date").val() && $("#room_id").val()) {
                checkOverlap();
            }
        });

        // Fungsi untuk mengecek overlap booking
        function checkOverlap() {
            var date = $("#date").val();
            var room = $("#room_id").val();
            var startTime = $("#start_time").val();
            var endTime = $("#end_time").val();

            // Tampilkan loading indicator
            var timeInputs = $('#start_time, #end_time');
            timeInputs.css('border-color', '#ced4da');
            $('#time-warning').remove();
            $('#time-success').remove();

            if (date && room && startTime && endTime) {
                $.ajax({
                    url: '{{ route('day-times.check-overlap') }}',
                    dataType: 'JSON',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        date: date,
                        room: room,
                        start_time: startTime,
                        end_time: endTime
                    },
                    success: function(response) {
                        if (response.overlap) {
                            // Ada overlap, tampilkan warning
                            timeInputs.css('border-color', '#dc3545');
                            $('#start_time').after(`
                <div id="time-warning" class="text-danger small mt-1">
                  <i class="fas fa-exclamation-circle"></i> ${response.message}
                </div>
              `);
                        } else {
                            // Tidak ada overlap, tampilkan success
                            timeInputs.css('border-color', '#28a745');
                            $('#start_time').after(`
                <div id="time-success" class="text-success small mt-1">
                  <i class="fas fa-check-circle"></i> ${response.message}
                </div>
              `);
                        }
                    },
                    error: function() {
                        timeInputs.css('border-color', '#ffc107');
                    }
                });
            }
        }

        // Event listener untuk perubahan input waktu
        $('#start_time, #end_time').on('change keyup', function() {
            checkOverlap();
        });

        // Event listener untuk perubahan ruangan atau tanggal
        $('#date, #room_id').on('change', function() {
            checkOverlap();
        });

        // Validasi form sebelum submit
        $("#form-booking").submit(function(e) {
            var startTime = $("#start_time").val();
            var endTime = $("#end_time").val();
            var date = $("#date").val();
            var room = $("#room_id").val();

            // Cek jika waktu belum diisi
            if (!startTime || !endTime) {
                e.preventDefault();
                alert('Silakan isi jam mulai dan jam selesai booking.');
                return false;
            }

            // Cek jika waktu mulai > waktu selesai
            if (startTime >= endTime) {
                e.preventDefault();
                alert('Jam mulai harus lebih awal dari jam selesai.');
                return false;
            }

            // Cek jika ruangan belum dipilih
            if (!room) {
                e.preventDefault();
                alert('Silakan pilih ruangan terlebih dahulu.');
                return false;
            }

            // Cek jika tanggal belum dipilih
            if (!date) {
                e.preventDefault();
                alert('Silakan pilih tanggal booking terlebih dahulu.');
                return false;
            }

            // Cek overlap dengan AJAX sync
            var isOverlap = false;
            $.ajax({
                url: '{{ route('day-times.check-overlap') }}',
                dataType: 'JSON',
                type: 'POST',
                async: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    date: date,
                    room: room,
                    start_time: startTime,
                    end_time: endTime
                },
                success: function(response) {
                    if (response.overlap) {
                        isOverlap = true;
                        alert(response.message);
                    }
                }
            });

            if (isOverlap) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endpush

@include('includes.notification')
