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
<!-- FILTER SECTION -->
<div class="card mb-4">
  <div class="card-header">
    <h4><i class="fas fa-filter"></i> Filter Data</h4>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>Filter Berdasarkan Hari</label>
          <select id="filter-day" class="form-control">
            <option value="">-- Semua Hari --</option>
            <option value="Senin">Senin</option>
            <option value="Selasa">Selasa</option>
            <option value="Rabu">Rabu</option>
            <option value="Kamis">Kamis</option>
            <option value="Jumat">Jumat</option>
            <option value="Sabtu">Sabtu</option>
            <option value="Minggu">Minggu</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Filter Berdasarkan Ruangan</label>
          <select id="filter-room" class="form-control">
            <option value="">-- Semua Ruangan --</option>
            @foreach($rooms as $room)
            <option value="{{ $room->name }}">{{ $room->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Filter Berdasarkan Tanggal</label>
          <input type="date" id="filter-date" class="form-control" placeholder="Pilih Tanggal">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button id="btn-filter" class="btn btn-primary"><i class="fas fa-search"></i> Terapkan Filter</button>
        <button id="btn-reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset Filter</button>
      </div>
    </div>
  </div>
</div>

@component('components.datatables')
@slot('table_id', 'booking-list-table')
@slot('table_header')
<tr>
  <th>No</th>
  <th>Ruangan</th>
  <th>Nama</th>
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
    // Inisialisasi DataTable dengan serverSide: true untuk filter server-side
    const table = $('#booking-list-table').DataTable({
      processing: true,
      serverSide: true, // Ubah ke serverSide true untuk filter server-side
      ajax: {
        url: '/admin/booking-list/json',
        data: function(d) {
          // Kirim parameter filter ke server
          d.filter_day = $('#filter-day').val();
          d.filter_room = $('#filter-room').val();
          d.filter_date = $('#filter-date').val();
        }
      },
      order: [
        [3, 'asc'],
        [4, 'asc']
      ],
      columnDefs: [{
          targets: [3],
          type: 'date',
          orderData: [3, 4]
        },
        {
          targets: [4],
          orderData: [4, 3]
        },
        {
          targets: 6,
          render: $.fn.dataTable.render.ellipsis(20, true)
        },
      ],
      columns: [{
          data: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          data: 'room',
          orderable: false,
          render: function(data, type, row) {
            let result = data || '-';
            if (type === 'filter') return data ? data.toLowerCase() : '';

            const now = new Date();
            const dt = new Date(`${row.date}T${row.start_time}`);
            result += '<div class="table-links">';

            if (dt > now && (row.status === 'PENDING' || row.status === 'DITOLAK')) {
              result += ` 
              <a href="javascript:;" data-id="${row.id}" 
                 data-title="Setujui" data-body="Yakin setujui booking ini?" 
                 data-value="1" class="text-primary" id="acc-btn">Setujui</a>`;
              if (row.status === 'PENDING') {
                result += '<div class="bullet"></div>';
              }
            }

            if (row.status === 'PENDING' || row.status === 'DISETUJUI') {
              result += ` 
              <a href="javascript:;" data-id="${row.id}" 
                 data-title="Tolak" data-body="Yakin tolak booking ini?" 
                 data-value="0" class="text-danger" id="deny-btn">Tolak</a>`;
            }

            result += '</div>';
            return result;
          }
        },
        {
          data: 'user',
          orderable: false
        },
        {
          data: 'date_display',
          name: 'date'
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
          render: function(data) {
            const badgeClass = {
              'PENDING': 'info',
              'DISETUJUI': 'primary',
              'DIGUNAKAN': 'primary',
              'DITOLAK': 'danger',
              'EXPIRED': 'dark',
              'BATAL': 'warning',
              'SELESAI': 'success',
              'BOOKING_BY_LAB': 'info',
            } [data] || 'secondary';
            return `<span class="badge badge-${badgeClass}">${data}</span>`;
          }
        },
      ]
    });

    // Tombol Terapkan Filter
    $('#btn-filter').on('click', function() {
      table.ajax.reload();
    });

    // Tombol Reset Filter
    $('#btn-reset').on('click', function() {
      $('#filter-day').val('');
      $('#filter-room').val('');
      $('#filter-date').val('');
      table.ajax.reload();
    });

    // Modal konfirmasi (Setujui/Tolak)
    $(document).on('click', '#acc-btn, #deny-btn', function() {
      const id = $(this).data('id');
      const title = $(this).data('title');
      const body = $(this).data('body');
      const value = $(this).data('value');
      const submitClass = value === 1 ? 'btn btn-primary' : 'btn btn-danger';

      $('.modal-title').html(title);
      $('.modal-body').html(body);
      $('#confirm-form').attr('action', `/admin/booking-list/${id}/update/${value}`);
      $('#confirm-form').attr('method', 'POST');
      $('#submit-btn').attr('class', submitClass);
      $('#lara-method').attr('value', 'put');
      $('#confirm-modal').modal('show');
    });

    // Lightbox gambar
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