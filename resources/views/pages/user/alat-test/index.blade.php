@extends('layouts.main')

@section('title', 'Data Alat Test - ROOMING')

@section('header-title', 'Data Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Alat Test</a></div>
  <div class="breadcrumb-item active">Data Alat Test</div>
@endsection

@section('section-title', 'Alat Test')

@section('section-lead')
  Berikut ini adalah daftar seluruh Alat Test.
@endsection

@section('content')

  @component('components.datatables')
    @slot('table_id', 'alat-test-table')
    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Foto</th>
        <th>Serial Number</th>
        <th>Nama</th>
        <th>Deskripsi</th>
        <th>Booking</th>
      </tr>
    @endslot
  @endcomponent

@endsection

@push('after-script')

<script>
  $(document).ready(function() {
    $('#alat-test-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('alat-test-list.json') }}',
      order: [2, 'asc'],
      columns: [
        {
          name: 'DT_RowIndex',
          data: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          name: 'photo',
          data: 'photo',
          orderable: false,
          searchable: false,
          render: function(data, type, row) {
            if (data != null) {
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
          name: 'serial_number',
          data: 'serial_number',
        },
        {
          name: 'name',
          data: 'name',
        },
        {
          name: 'description',
          data: 'description',
        },
        {
          name: 'bookings',
          data: 'bookings',
        },
      ],
    });

    // Aktifkan lightbox saat gambar diklik
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  });
</script>

{{-- Lightbox plugin --}}
@include('includes.lightbox')

@endpush
