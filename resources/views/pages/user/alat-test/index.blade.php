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
    @slot('table_id', 'alat-booking-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Nama Alat</th>
        <th>Tanggal Peminjaman</th>
        <th>Keperluan</th>
        <th>Status</th>
      </tr>
    @endslot
  @endcomponent

@endsection

@push('after-script')
<script>
  $(document).ready(function() {
    $('#alat-booking-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('alat-test.json') }}',
      order: [2, 'desc'],
      columns: [
        {
          name: 'DT_RowIndex',
          data: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          name: 'alat_test',
          data: 'alat_test'
        },
        {
          name: 'borrow_date',
          data: 'borrow_date'
        },
        {
          name: 'purpose',
          data: 'purpose'
        },
        {
          name: 'status',
          data: 'status',
          render: function(data) {
            if (data === 'approved') {
              return '<span class="badge badge-success">Disetujui</span>';
            } else if (data === 'pending') {
              return '<span class="badge badge-warning">Menunggu</span>';
            } else if (data === 'rejected') {
              return '<span class="badge badge-danger">Ditolak</span>';
            } else {
              return '<span class="badge badge-secondary">' + data + '</span>';
            }
          }
        },
      ]
    });
  });
</script>
@endpush
