@extends('layouts.main')

@section('title')
  Buat Booking Alat Test - ROOMING
@endsection 

@section('header-title')
  Buat Booking
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item"><a href="{{ route('my-booking-alat-test-list.index') }}">My Booking Alat Test</a></div>
  <div class="breadcrumb-item active">
    Edit Booking
  </div>
@endsection

@section('section-title')
  Edit Booking Alat Test
@endsection 
    
@section('section-lead')
  Silakan isi form di bawah ini untuk mengupdate booking Alat Test.
@endsection

@section('content')

<div class="row">
  <div class="col ">
    <div class="card">
      <div class="card-body">

        <form action="{{ route('my-booking-alat-test-list.update', [ $tool->id ]) }}" method="post" name="form-booking" id="form-booking">
          @method('patch')
          @csrf

          <div class="form-group">
            <label for="date">Tanggal Booking</label>
            <input type="text" name="date" class="form-control datepicker" data-min-date="{{ $nowdate }}" id="date" value="{{ $tool->date }}">
          </div>
          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="time_start_hour">Waktu Booking dari</label>
              <select name="time_start[]" id="time_start_hour" class="form-control">
                <option value="" selected>Choose...</option>
                @foreach ($hours as $hour)
                  <option value="{{ $hour }}" {{ date("H", strtotime($tool->start_time)) == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="time_start_minute">&nbsp;</label>
              <select name="time_start[]" id="time_start_minute" class="form-control">
                <option value="" selected>Choose...</option>
                @foreach ($minutes as $minute)
                  <option value="{{ $minute }}" {{ date("i", strtotime($tool->start_time)) == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md-2">
              &nbsp;
            </div>

            <div class="form-group col-md-2">
              <label for="time_end_hour">Sampai</label>
              <select name="time_end[]" id="time_end_hour" class="form-control">
                <option value="" selected>Choose...</option>
                @foreach ($hours as $hour)
                  <option value="{{ $hour }}" {{ date("H", strtotime($tool->end_time)) == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="time_end_minute">&nbsp;</label>
              <select name="time_end[]" id="time_end_minute" class="form-control">
                <option value="" selected>Choose...</option>
                @foreach ($minutes as $minute)
                  <option value="{{ $minute }}" {{ date("i", strtotime($tool->end_time)) == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="purpose">Keperluan</label>
            <textarea name="purpose" class="form-control" id="purpose" style="height: 185px;">{{ $tool->purpose }}</textarea>
          </div>

          <div class="form-group">
            <label for="purpose">Alat Test</label>
            <select class="js-example-basic-single" id="alat-items" data-placeholder="Pilih alat test" data-allow-clear="true" name="state">
              <option value=""></option>
            </select>
          </div>

          <table class="table">
            <thead>
              <tr>
                <th>Serial Number</th>
                <th>aksi</th>
              </tr>
            </thead>
            <tbody id="list-alat">
              @foreach ($tool->alatTestItemBooking as $key => $item)
                <tr class="item-{{ $item->alatTestItem->id }}" id="item-{{ $item->alatTestItem->id }}">
                  <td>
                    <input type="hidden" name="items[]" value="{{ $item->alatTestItem->id }}" />
                    {{ $item->alatTestItem->alatTest->name }} ({{ $item->alatTestItem->serial_number }})
                  </td>
                  <td><a href="javascript:void(0)" onclick="removeItem({{ $item->alatTestItem->id }})"><i class="fas fa-trash"></i></a></td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <div class="card-footer  text-right ">
            <button class="btn btn-primary">Simpan</button>
            <a type="button" href="{{ route('my-booking-alat-test-list.show', [ $tool->id ]) }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection

@section('modal-box')
 
@endsection

@push('after-style')
  {{-- datepicker  --}}
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  {{-- datepicker  --}}
@endpush

@push('after-script')
  {{-- datepicker  --}}
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  {{-- datepicker  --}}

  <script>
    $(document).ready(function() {
      $('.js-example-basic-single').select2();

      window.getTools = function () {
      var date = $("#date").val();
      var start_hour = $("#time_start_hour").val();
      var start_minute = $("#time_start_minute").val();
      var end_hour = $("#time_end_hour").val();
      var end_minute = $("#time_end_minute").val();
      var ajaxurl = "/alat-test-list/get-tools";

      var start = start_hour +":"+ start_minute;
      var end = end_hour +":"+ end_minute;

      if(date != "" && start_hour != "" && start_minute != "" && end_hour != "" && end_minute != "") {
        $.ajax({
          url: ajaxurl, 
          dataType: 'JSON', 
          type: 'GET', 
          async: false, 
          data: {
            date: date, 
            start: start, 
            end: end
          }, 
          success: function success(response) {
            if(response.status == 'success') {
              if(response.data != null) {
                let tools = response.data.tools
                let bookings = response.data.bookings
                let html = `<option value="">Pilih alat test</option>`;
                let booked = '';
                
                tools.map( (item, index) => {
                  if(bookings && bookings.length > 0) {
                    booked = '';
                    if(bookings.indexOf(item.id) != -1) {
                      booked = '(Booked)';
                    }
                  }

                  html += `
                    <option `+ (booked != '' ? 'disabled' : '') +` value="`+ item.id +`">`+ item.alat_test.name +` (`+ item.serial_number +`) `+ booked +`</option>
                  `;
                });

                $("#alat-items").html(html);
              }
            } else {
              $("#alat-items").html(`<option value="">Pilih alat test</option>`);
              console.log(response.message);
            }
          }
        });
      } else {
        $("#alat-items").html(`<option value="">Pilih alat test</option>`);
      }
    }

      $('#alat-items').on("change", function() {
        var id = $(this).val();
        var text = $("#alat-items option:selected").text();

        var html = `
          <tr class="item-`+ id +`" id="item-`+ id +`">
            <td>
              <input type="hidden" name="items[]" value="`+ id +`" />
              `+ text +`
            </td>
            <td><a href="javascript:void(0)" onclick="removeItem(`+ id +`)"><i class="fas fa-trash"></i></a></td>
          </tr>
        `;

        if(id != "" && $("#item-"+ id).length == 0) {
          $("#list-alat").append(html);
          // $(this).val("").trigger('change'); // to clear select2
        }
      });

      window.removeItem = function(id) {
        $("#item-"+ id).remove();
      }

      $("#date").on('change', function() {
        getTools();
      });

      $("#time_start_hour").on('change', function() {
        getTools();
      });

      $("#time_start_minute").on('change', function() {
        getTools();
      });

      $("#time_end_hour").on('change', function() {
        getTools();
      });

      $("#time_end_minute").on('change', function() {
        getTools();
      });

      getTools();

    });
  </script>
@endpush

@include('includes.notification')