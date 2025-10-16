@extends('layouts.main')

@section('title')
  In Out Alat Test - ROOMING
@endsection 

@section('header-title')
  Keluar Masuk Alat Test
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item"><a href="{{ route('alat-test.inout.index') }}">Alat Test</a></div>
  <div class="breadcrumb-item active">
    Buat In Out
  </div>
@endsection

@section('section-title')
  Buat In Out Alat Test
@endsection 
    
@section('section-lead')
  Silakan isi form di bawah ini untuk membuat In Out Alat Test.
@endsection

@section('content')

<div class="row">
  <div class="col ">
    <div class="card">
      <div class="card-body">

        <form action="{{ route('alat-test.inout.store') }}" method="post" name="form-inout" id="form-inout">
          @csrf

          <div class="form-group">
            <label for="date">Tanggal</label>
            <input type="text" name="date" class="form-control datepicker" data-min-date="{{ $nowdate }}" id="date">
          </div>
          <div class="form-group">
            <label for="purpose">Keterangan</label>
            <textarea name="description" class="form-control" id="description" style="height: 185px;"></textarea>
          </div>
          <div class="form-group">
            <label class="input-label">Tipe</label>
    
            <div class="col-sm-10">
              @foreach($types as $key => $value)
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" id="type-{{ $key }}" value="{{ $key }}" {{ $key == (old('type') ?? "Masuk") ? 'checked' : '' }}>
                  <label class="form-check-label" for="type-{{ $key }}">
                    {{ $value }}
                  </label>
                </div>
              @endforeach
            </div>
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
                <th>jumlah</th>
                <th>aksi</th>
              </tr>
            </thead>
            <tbody id="list-alat">
              
            </tbody>
          </table>

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
        $.ajax({
          url: 'get-tools', 
          dataType: 'JSON', 
          type: 'GET', 
          async: false, 
          success: function success(response) {
            if(response.status == 'success') {
              if(response.data != null) {
                let tools = response.data
                let html = `<option value="">Pilih alat test</option>`;
                
                tools.map( (item, index) => {
                  html += `
                    <option value="`+ item.id +`">`+ item.alat_test.name +` (`+ item.serial_number +`) </option>
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
            <td>
              <input type="number" name="amount[]" value="0" />
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

      getTools();

    });
  </script>
@endpush

@include('includes.notification')