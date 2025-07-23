@extends('layouts.main')

@section('title', 'Daftar Peminjaman Alat Test - ROOMING')
@section('header-title', 'Peminjaman Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Peminjaman Alat Test</div>
@endsection

@section('section-title', 'Peminjaman Alat Test')
@section('section-lead', 'Berikut ini adalah daftar alat test yang pernah Anda pinjam.')

@section('content')

  @component('components.datatables')

    @slot('buttons')
      <a href="{{ route('my-booking-alat-test-list.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>&nbsp;Pinjam Alat
      </a>
    @endslot

    @slot('table_id', 'booking_alats')

    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Alat</th>
        <th>Tanggal</th>
        <th>Keperluan</th>
        <th>Status</th>
      </tr>
    @endslot

  @endcomponent

@endsection

@push('after-script')
<script>
  $(document).ready(function() {
    $('#booking_alats').DataTable({
        processing: true,
        serverSide: false,
        ajax: '/my-booking-alat-test-list/json',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'alat_test', name: 'alat_test' },
            { data: 'date', name: 'date' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'purpose', name: 'purpose' },
            { data: 'status', name: 'status' }
        ]
    });
  });
</script>
@endpush
