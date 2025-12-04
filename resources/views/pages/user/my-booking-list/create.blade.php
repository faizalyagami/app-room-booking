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
                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
              @endforeach
            </select>
            @error('room_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="date">Tanggal Booking</label>
            <input type="text" name="date" class="form-control datepicker" data-min-date="{{ $nowdate }}" id="date" required>
            @error('date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="time">Waktu Booking</label>
            <select name="time" class="form-control" id="time" required>
              <option value="">Pilih Waktu</option>
            </select>
            @error('time')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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

      // Load times if date and room are already selected (after validation error)
      if($("#date").val() && $("#room_id").val()) {
        getTimes();
      }
    });

    window.getTimes = function () {
      var date = $("#date").val();
      var room = $("#room_id").val();
      var ajaxurl = "{{ route('day-times.get-times') }}"; // Gunakan route name

      // Clear previous options
      $("#time").html(`<option value="">Memuat waktu...</option>`);

      if(date != "" && room != "") {
        $.ajax({
          url: ajaxurl, 
          dataType: 'JSON', 
          type: 'GET', 
          data: {
            date: date, 
            room: room
          }, 
          success: function(response) {
            if(response.status == 'success') {
              var days = response.data;
              let html = `<option value="">Pilih Waktu</option>`;

              if(days != null && days.times.length > 0) {
                let bookings = days.bookings || [];
                
                // Buat mapping booked slots dengan status
                let bookedSlots = {};
                bookings.forEach(function(booking) {
                  bookedSlots[booking.start_time] = booking.status;
                });

                days.times.forEach(function(item) {
                  let start = item.start_time.substring(0, item.start_time.lastIndexOf(':'));
                  let end = item.end_time.substring(0, item.end_time.lastIndexOf(':'));
                  let startTimeFull = item.start_time;
                  let bookedStatus = bookedSlots[startTimeFull];
                  let isDisabled = false;
                  let label = '';
                  let title = '';

                  if (bookedStatus) {
                    isDisabled = true;
                    // Tentukan label dan warna berdasarkan status
                    if (bookedStatus === 'BOOKING_BY_LAB') {
                      label = ' (Booked by Lab)';
                      title = 'Slot waktu ini sudah dibooking oleh laboratorium';
                    } else {
                      label = ' (Booked)';
                      title = 'Slot waktu ini sudah dibooking';
                    }
                  }

                  html += `
                    <option ${isDisabled ? 'disabled style="color: #6c757d; background-color: #e9ecef; cursor: not-allowed;"' : ''} 
                            value="${start} - ${end}"
                            title="${title}">
                      ${start} - ${end}${label}
                    </option>
                  `;
                });
              } else {
                html = `<option value="">Tidak ada waktu tersedia</option>`;
              }

              $("#time").html(html);
            } else {
              $("#time").html(`<option value="">${response.message || 'Gagal memuat waktu'}</option>`);
              console.error('Error from server:', response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error("Error fetching times:", error);
            $("#time").html(`<option value="">Terjadi kesalahan saat memuat waktu</option>`);
            
            // Tampilkan notifikasi error
            if(xhr.responseJSON && xhr.responseJSON.message) {
              showNotification('error', xhr.responseJSON.message);
            }
          }
        });
      } else {
        $("#time").html(`<option value="">Pilih Ruangan dan Tanggal terlebih dahulu</option>`);
      }
    }

    $("#date").on('change', function() {
      getTimes();
    });

    $("#room_id").on('change', function() {
      getTimes();
    });

    // Fungsi untuk menampilkan notifikasi
    function showNotification(type, message) {
      // menggunakan library notifikasi seperti Toastr
      // Atau tampilkan alert sederhana
      alert(message);
    }

    // Validasi form sebelum submit
    $("#form-booking").submit(function(e) {
      var timeValue = $("#time").val();
      var timeOption = $("#time option:selected");
      
      // Cek jika waktu yang dipilih disabled (sudah dibooking)
      if (timeOption.is(':disabled')) {
        e.preventDefault();
        alert('Waktu yang dipilih sudah dibooking. Silakan pilih waktu lain.');
        return false;
      }
      
      // Cek jika waktu belum dipilih
      if (!timeValue) {
        e.preventDefault();
        alert('Silakan pilih waktu booking.');
        return false;
      }
    });
  </script>
@endpush

@include('includes.notification')