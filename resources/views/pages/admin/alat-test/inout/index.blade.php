@extends('layouts.main')

@section('title', 'Keluar Masuk Alat Test - ROOMING')

@section('header-title', 'Data Keluar Masuk Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Data Keluar Masuk Alat Test</div>
@endsection

@section('section-title', 'Keluar Masuk Alat Test')

@section('section-lead')
  Berikut ini adalah daftar seluruh Keluar Masuk alat test yang telah dilakukan.
@endsection

@section('content')

  @component('components.datatables')
    @slot('buttons')
      <a href="{{ route('alat-test.inout.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Buat Baru</a>
    @endslot

    @slot('table_id', 'alat-test-inout-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Tanggal</th>
        <th>Tipe</th>
        <th>Items</th>
        <th>Aksi</th>
      </tr>
    @endslot
  @endcomponent

@endsection

@push('after-script')
<script src="//cdn.datatables.net/plug-ins/1.10.22/dataRender/ellipsis.js"></script>

  <script>
  $(document).ready(function() {

    $('#alat-test-inout-table').DataTable({
      processing: true,
      ajax: '{{ route('alat-test.inout.json') }}',
      columns: [
        {
          name: 'index',
          data: 'index',
          orderable: false, 
          searchable: false,
        },
        {
          name: 'date',
          data: 'date',
        },
        {
          name: 'type',
          data: 'type'
        },
        {
          name: 'count',
          data: 'count'
        },
        {
          data: 'id', 
          orderable: false,
          render: function(id) {
            return `
              <div class="table-links">
                <a href="inout/${id}/show" class="text-info">Detail</a>
              </div>`;
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
      $('#confirm-form').attr('action', '/alat-test/list/'+id+'/cancel');
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
