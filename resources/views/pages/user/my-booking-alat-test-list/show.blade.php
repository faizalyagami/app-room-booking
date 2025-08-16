@extends('layouts.main')

@section('title')
  Buat Booking Alat Test - ROOMING
@endsection 

@section('header-title')
  Detail Booking Alat Test
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item"><a href="{{ route('my-booking-alat-test-list.index') }}">My Booking Alat Test</a></div>
  <div class="breadcrumb-item active">
    Detail Booking
  </div>
@endsection

@section('section-title')
  Detail Booking Alat Test
@endsection 
    
@section('section-lead')
  Berikut adalah detail dari booking alat test.
@endsection

@section('content')

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="card-footer  text-right ">
            @if(in_array($tool->status, ["PENDING"]))
              <a href="javascript:void(0)" class="btn btn-danger" data-id="{{ $tool->id }}" data-title="Batalkan" data-body="Yakin batalkan booking ini?" id="cancel-btn"><i class="fas fa-times"></i>&nbsp;Batal</a>
              <a href="{{ route('my-booking-alat-test-list.edit', [$tool->id]) }}" class="btn btn-primary"><i class="fas fa-pen"></i>&nbsp;Edit</a>
            @endif
          </div>
          <table class="table table-striped">
            <tr>
                <th width="150px">Tanggal</th>
                <td>{{ date("d m Y", strtotime($tool->date)) }}</td>
            </tr>
            <tr>
                <th>Time</th>
                <td>{{ date("H:i", strtotime($tool->start_time)) }} - {{ date("H:i", strtotime($tool->end_time)) }}</td>
            </tr>
            <tr>
                <th>Tujuan</th>
                <td>{{ $tool->purpose }}</td>
            </tr>
          </table>          
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nama</th>
                <th scope="col">Serial Number</th>
              </tr>
            </thead>
            <tbody>
              @if(count($tool->alatTestItemBooking))
                @foreach ($tool->alatTestItemBooking as $key => $item)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $item->alatTestItem->alatTest->name }}</td>
                      <td>{{ $item->alatTestItem->serial_number }}</td>
                    </tr>
                @endforeach
              @endif
            </tbody>
          </table>          
        </div>
      </div>

    </div>
  </div>

@endsection

@push('after-script')

  <script>
  $(document).ready(function() {
    $(document).on('click', '#cancel-btn', function() {
      var id    = $(this).data('id'); 
      var title = $(this).data('title');
      var body  = $(this).data('body');

      var submit_btn_class = 'btn btn-danger';

      $('.modal-title').html(title);
      $('.modal-body').html(body);
      $('#confirm-form').attr('action', '/my-booking-alat-test-list/'+id+'/cancel');
      $('#confirm-form').attr('method', 'POST');
      $('#submit-btn').attr('class', submit_btn_class);
      $('#lara-method').attr('value', 'put');
      $('#confirm-modal').modal('show');
    });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
  });

</script>

@include('includes.lightbox')

@include('includes.notification')

@include('includes.confirm-modal')

@endpush