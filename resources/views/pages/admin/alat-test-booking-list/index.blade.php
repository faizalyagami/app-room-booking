@extends('layouts.main')

@section('title', 'Alat Test Booking List - ROOMING')

@section('header-title', 'Alat Test Booking List')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Alat Test Booking List</div>
@endsection

@section('section-title', 'Alat Test Booking List')

@section('section-lead')
  Berikut ini adalah daftar seluruh booking alat test dari setiap user.
@endsection

@section('content')

  @component('components.datatables')
    
    @slot('table_id', 'alat-test-booking-table')
    
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Tanggal</th>
        <th>Waktu Mulai</th>
        <th>Waktu Selesai</th>
        <th>Keperluan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    @endslot
    
  @endcomponent

@endsection

@push('after-script')
<script>
  $(document).ready(function() {
    $('#alat-test-booking-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('alat-test-booking-list.json') }}',
      columns: [
        {
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          data: 'user.name',
          name: 'user.name'
        },
        {
          data: 'date_formatted',
          name: 'date'
        },
        {
          data: 'start_time',
          name: 'start_time'
        },
        {
          data: 'end_time',
          name: 'end_time'
        },
        {
          data: 'purpose',
          name: 'purpose',
          render: function(data) {
            return data.length > 50 ? data.substr(0, 50) + '...' : data;
          }
        },
        {
          data: 'status_badge',
          name: 'status',
          orderable: true,
          searchable: true
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        }
      ],
      order: [[2, 'desc']], // Order by tanggal
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ entri",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
        paginate: {
          previous: "Sebelumnya",
          next: "Selanjutnya"
        }
      }
    });
  });
</script>

@include('includes.notification')

@endpush