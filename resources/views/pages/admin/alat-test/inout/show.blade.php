@extends('layouts.main')

@section('title')
  Keluar Masuk Alat Test - ROOMING
@endsection 

@section('header-title')
  Detail Keluar Masuk Alat Test
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item"><a href="{{ route('alat-test.inout.index') }}">Keluar Masuk Alat Test</a></div>
  <div class="breadcrumb-item active">
    Detail keluar Masuk Alat Test
  </div>
@endsection

@section('section-title')
Detail keluar Masuk Alat Test
@endsection 
    
@section('section-lead')
  Berikut adalah Detail dari keluar Masuk Alat Test.
@endsection

@section('content')

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped">
            <tr>
                <th width="150px">Tanggal</th>
                <td>{{ date("d F Y", strtotime($item->date)) }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $item->description }}</td>
            </tr>
            <tr>
                <th>Tipe</th>
                <td>{{ $item->type }}</td>
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
                <th scope="col" style="text-align: end;">Jumlah</th>
              </tr>
            </thead>
            <tbody>
              @if(count($item->alatTestInoutItems))
                @foreach ($item->alatTestInoutItems as $key => $item)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $item->alatTestItem->alatTest->name }}</td>
                      <td>{{ $item->alatTestItem->serial_number }}</td>
                      <td align="right">{{ number_format($item->quantity, 0, ",", ".") }}</td>
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