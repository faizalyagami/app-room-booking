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
            <select name="room_id" class="form-control" id="room_id">
              <option value="">Pilih Ruangan</option>
              @foreach ($rooms as $room)
                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="date">Tanggal Booking</label>
            <input type="text" name="date" class="form-control datepicker" data-min-date="{{ $nowdate }}" id="date">
          </div>
          <div class="form-group">
            <label for="time">Waktu Booking</label>
            <select name="time" class="form-control" id="time">
              <option value="">Pilih Waktu</option>
            </select>
          </div>
          <div class="form-group">
            <label for="purpose">Keperluan</label>
            <textarea name="purpose" class="form-control" id="purpose" style="height: 185px;"></textarea>
          </div>
          <div class="card-footer  text-right ">
            <button class="btn btn-primary">Simpan</button>
            <a type="button" href="{{ route('my-booking-list.index') }}" class="btn btn-secondary">Cancel</a>
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
    window.getTimes = function () {
      var date = $("#date").val();
      var room = $("#room_id").val();
      var ajaxurl = "/day-times/get-times";

      if(date != "" && room != "") {
        $.ajax({
          url: ajaxurl, 
          dataType: 'JSON', 
          type: 'GET', 
          async: false, 
          data: {
            date: date, 
            room: room
          }, 
          success: function success(response) {
            var days = response.data;

            if(response.status == 'success') {
              if(days != null) {
                let bookings = days.bookings;
                let html = `<option value="">Pilih Waktu</option>`;
                let booked = '';
                
                days.times.map( (item, index) => {
                  let start = item.start_time.substring(0, item.start_time.lastIndexOf(':'));
                  let end = item.end_time.substring(0, item.end_time.lastIndexOf(':'));

                  if(bookings && bookings.length > 0) {
                    booked = '';
                    if(bookings.indexOf(item.start_time) != -1) {
                      booked = '(Booked)';
                    }
                  }

                  html += `
                    <option `+ (booked != '' ? 'disabled' : '') +` value="`+ start +` - `+ end +`">`+ start +` - `+ end +` `+ booked +`</option>
                  `;
                });

                $("#time").html(html);
              }
            } else {
              $("#time").html(`<option value="">Pilih Waktu</option>`);
              console.log(response.message);
            }
          }
        });
      } else {
        $("#time").html(`<option value="">Pilih Waktu</option>`);
      }
    }

    $("#date").on('change', function() {
      getTimes();
    });

    $("#room_id").on('change', function() {
      getTimes();
    });
  </script>
@endpush

@include('includes.notification')