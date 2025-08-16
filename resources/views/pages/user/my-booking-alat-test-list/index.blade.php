@extends('layouts.main')

@section('title', 'Peminjaman Alat Test - ROOMING')

@section('header-title', 'Data Peminjaman Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Data Peminjaman Alat Test</div>
@endsection

@section('section-title', 'Peminjaman Alat Test')

@section('section-lead')
  Berikut ini adalah daftar seluruh peminjaman alat test yang telah dilakukan.
@endsection

@section('content')

  @component('components.datatables')
    @slot('buttons')
      <a href="{{ route('my-booking-alat-test-list.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Booking</a>
    @endslot

    @slot('table_id', 'booking-alat-test-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Aksi</th>
        <th>Tanggal</th>
        <th>Waktu Mulai</th>
        <th>Waktu Selesai</th>
        <th>Keperluan</th> 
        <th>Status</th>
      </tr>
    @endslot
  @endcomponent

@endsection

@push('after-script')
<script src="//cdn.datatables.net/plug-ins/1.10.22/dataRender/ellipsis.js"></script>

  <script>
  $(document).ready(function() {

    $('#booking-alat-test-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('my-booking-alat-test-list.json') }}',
      columnDefs: [ 
        {
            targets: [ 3 ],
            orderData: [ 3, 4 ]
        }, 
        {
            targets: [ 4 ],
            orderData: [ 4, 3 ]
        },
        {
          targets: 6,
          render: $.fn.dataTable.render.ellipsis( 20, true )
        }, 
      ],
      order: [[3, 'desc'], [4, 'desc']],
      columns: [
        {
          name: 'DT_RowIndex',
          data: 'DT_RowIndex',
          orderable: false, 
          searchable: false,
        },
        {
          data: 'id', 
          orderable: false,
          render: function(id) {
            return `
              <div class="table-links">
                <a href="my-booking-alat-test-list/${id}/show" class="text-info">Detail</a>
              </div>`;
          }
        },
        {
          name: 'date',
          data: 'date',
        },
        {
          name: 'start_time',
          data: 'start_time',
        },
        {
          name: 'end_time',
          data: 'end_time',
        },
        {
          name: 'purpose',
          data: 'purpose',
        },
        {
          name: 'status',
          data: 'status',
          render: function ( data, type, row ) {
            var result = `<span class="badge badge-`;

            if(data === 'PENDING') 
              result += `info`;
            else if(data === 'DISETUJUI')
              result += `primary`;
            else if(data === 'DITOLAK')
              result += `danger`;
            else if(data === 'EXPIRED')
              result += `warning`;
            else if(data === 'BATAL')
              result += `warning`;
            else if(data === 'SELESAI')
              result += `success`;

            result += `">${data}</span>`;

            return result;
          } 
        },
      ],
    });

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
