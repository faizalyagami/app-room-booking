@extends('layouts.main')

@section('title', 'Booking List - ROOMING')

@section('header-title', 'Booking List')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Booking List</div>
@endsection

@section('section-title', 'Booking List')

@section('section-lead')
  Berikut ini adalah daftar seluruh booking dari setiap user.
@endsection

@section('content')
  @component('components.datatables')
    @slot('table_id', 'booking-list-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Foto</th>
        <th>Ruangan</th>
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
    ajax: '{{ route('booking-list.json') }}',
    columnDefs: [
      {
        targets: [4],
        orderData: [4, 5]
      },
      {
        targets: [5],
        orderData: [5, 4]
      },
      {
        targets: 7,
        render: $.fn.dataTable.render.ellipsis(20, true)
      },
    ],
    order: [[4, 'desc'], [5, 'desc']],
    columns: [
      {
        data: 'DT_RowIndex',
        orderable: false,
        searchable: false
      },
      {
        data: 'room.photo',
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          if (data) {
            return `<div class="gallery gallery-fw">
                      <a href="/storage/${data}" data-toggle="lightbox">
                        <img src="/storage/${data}" class="img-fluid" style="min-width: 80px; height: auto;">
                      </a>
                    </div>`;
          } else {
            return '-';
          }
        }
      },
      {
        data: 'room.name',
        orderable: false,
        render: function (data, type, row) {
          let result = data;
          const now = new Date();
          const dt = new Date(`${row.date}T${row.start_time}`);
          result += '<div class="table-links">';

          if (dt > now && (row.status === 'PENDING' || row.status === 'DITOLAK')) {
            result += ` <a href="javascript:;" data-id="${row.id}" 
                            data-title="Setujui" data-body="Yakin setujui booking ini?" 
                            data-value="1" class="text-primary" id="acc-btn">Setujui</a>`;
          }

          if (row.status === 'PENDING') {
            result += '<div class="bullet"></div>';
          }

          if (row.status === 'PENDING' || row.status === 'DISETUJUI') {
            result += ` <a href="javascript:;" data-id="${row.id}" 
                            data-title="Tolak" data-body="Yakin tolak booking ini?" 
                            data-value="0" class="text-danger" id="deny-btn">Tolak</a>`;
          }

          result += '</div>';
          return result;
        }
      },
      {
        data: 'user.name',
        orderable: false
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
            'PENDING': 'info',
            'DISETUJUI': 'primary',
            'DIGUNAKAN': 'primary',
            'DITOLAK': 'danger',
            'EXPIRED': 'warning',
            'BATAL': 'warning',
            'SELESAI': 'success'
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
