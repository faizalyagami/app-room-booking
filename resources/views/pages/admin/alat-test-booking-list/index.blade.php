@extends('layouts.main')

@section('title', 'Alat Test Booking List - ROOMING')

@section('header-title', 'Alat Test Booking List')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Alat Test Booking List 1</div>
@endsection

@section('section-title', 'Alat Test Booking List')

@section('section-lead')
  Berikut ini adalah daftar seluruh booking alat test dari setiap user.
@endsection

@section('content')
  @component('components.datatables')
    @slot('table_id', 'booking-list-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>User</th>
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
  $('#booking-list-table').DataTable({
    processing: true,
    serverSide: false, // client-side saja
    ajax: '{{ route('alat-test-booking-list.json') }}',
    columnDefs: [
      {
        targets: [2],
        orderData: [2, 3]
      },
      {
        targets: [3],
        orderData: [3, 2]
      },
      {
        targets: 3,
        render: $.fn.dataTable.render.ellipsis(20, true)
      },
    ],
    order: [[2, 'desc'], [3, 'desc']],
    columns: [
      {
        data: 'index',
        orderable: false,
        searchable: false
      },
      {
        data: 'user',
        orderable: false,
        render: function (data, type, row) {
          let result = data;
          const now = new Date();
          const dt = new Date(`${row.date}T${row.start_time}`);

          result += '<div class="table-links">';
          result += `<a href="alat-test-booking-list/${ row.id }/show" class="text-info">Detail</a>`;
          result += '</div>';

          return result;
        }
      },
      {
        data: 'date'
      },
      {
        data: 'start_time'
      },
      {
        data: 'end_time'
      },
      {
        data: 'purpose'
      },
      {
        data: 'status',
        render: function (data) {
          let badgeClass = {
            'tersedia': 'info',
            'dipinjam': 'primary',
          }[data] || 'secondary';

          return `<span class="badge badge-${badgeClass}">${data}</span>`;
        }
      },
    ]
  });

  $(document).on('click', '#acc-btn, #deny-btn', function() {
    let id = $(this).data('id');
    let title = $(this).data('title');
    let body = $(this).data('body');
    let value = $(this).data('value');
    let submitClass = value === 1 ? 'btn btn-primary' : 'btn btn-danger';

    $('.modal-title').html(title);
    $('.modal-body').html(body);
    $('#confirm-form').attr('action', `/admin/booking-list/${id}/update/${value}`);
    $('#confirm-form').attr('method', 'POST');
    $('#submit-btn').attr('class', submitClass);
    $('#lara-method').attr('value', 'put');
    $('#confirm-modal').modal('show');
  });

  $(document).on('click', '[data-toggle="lightbox"]', function(e) {
    e.preventDefault();
    $(this).ekkoLightbox();
  });
});
</script>

@include('includes.lightbox')
@include('includes.notification')
@include('includes.confirm-modal')
@endpush
