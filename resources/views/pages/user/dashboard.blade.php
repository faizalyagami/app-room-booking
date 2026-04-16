@extends('layouts.main')

@section('title', 'Dashboard - ROOMING')

@section('header-title', 'Dashboard')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Dashboard</a></div>
<div class="breadcrumb-item active">Dashboard</div>
@endsection

@section('content')
<div class="row">

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
    @slot('bg_color', 'bg-primary')
    @slot('icon', 'fas fa-calendar')
    @slot('title', 'Room Book Hari Ini')
    @slot('value', $booking_today)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
    @slot('bg_color', 'bg-success')
    @slot('icon', 'fas fa-calendar-alt')
    @slot('title', 'Room Book Semua')
    @slot('value', $booking_lifetime)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
    @slot('bg_color', 'bg-primary')
    @slot('icon', 'fas fa-calendar')
    @slot('title', 'Alat Test Book Hari Ini')
    @slot('value', $booking_tool_today)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
    @slot('bg_color', 'bg-success')
    @slot('icon', 'fas fa-calendar-alt')
    @slot('title', 'Alat Test Book Semua')
    @slot('value', $booking_tool_lifetime)
    @endcomponent
  </div>

</div>

<!-- INFO GAMBAR DARI ADMIN/LAB (TANPA PILIH RUANGAN) -->
@if(isset($infoImages) && $infoImages->count() > 0)
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle"></i> Informasi dari Laboratorium</h4>
        <small>Informasi penting dari admin/laboratorium</small>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($infoImages as $image)
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
              @if($image->title)
              <div class="card-header">
                <h5 class="mb-0">{{ $image->title }}</h5>
              </div>
              @endif
              <div class="card-body">
                <div class="gallery gallery-fw">
                  <a href="{{ asset('storage/' . $image->image) }}" data-toggle="lightbox" data-gallery="gallery-info">
                    <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                </div>
                @if($image->description)
                <div class="mt-3">
                  <small class="text-muted">{{ $image->description }}</small>
                </div>
                @endif
                @if($image->valid_from || $image->valid_until)
                <div class="mt-2">
                  <span class="badge badge-info">
                    <i class="fas fa-calendar-alt"></i>
                    Berlaku:
                    @if($image->valid_from) {{ date('d/m/Y', strtotime($image->valid_from)) }} @else - @endif
                    s/d
                    @if($image->valid_until) {{ date('d/m/Y', strtotime($image->valid_until)) }} @else - @endif
                  </span>
                </div>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Plot Ruangan dari Admin/Lab (Jika masih ingin dipertahankan) -->
@if(isset($roomsWithPlot) && $roomsWithPlot->count() > 0)
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-map-marked-alt"></i> Plot Ruangan dari Laboratorium</h4>
        <small>Informasi plot ruangan yang telah ditentukan oleh admin/laboratorium</small>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($roomsWithPlot as $room)
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="mb-0">{{ $room->name }}</h5>
              </div>
              <div class="card-body">
                <div class="gallery gallery-fw">
                  <a href="{{ asset('storage/' . $room->plot_image) }}" data-toggle="lightbox" data-gallery="gallery-plot">
                    <img src="{{ asset('storage/' . $room->plot_image) }}" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                </div>
                @if($room->plot_description)
                <div class="mt-3">
                  <small class="text-muted">{{ $room->plot_description }}</small>
                </div>
                @endif
                @if($room->plot_valid_from || $room->plot_valid_until)
                <div class="mt-2">
                  <span class="badge badge-info">
                    <i class="fas fa-calendar-alt"></i>
                    Berlaku:
                    @if($room->plot_valid_from) {{ date('d/m/Y', strtotime($room->plot_valid_from)) }} @else - @endif
                    s/d
                    @if($room->plot_valid_until) {{ date('d/m/Y', strtotime($room->plot_valid_until)) }} @else - @endif
                  </span>
                </div>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@component('components.datatables')

@slot('table_id', 'dashboard-booking-list-table')

@slot('card_header', 'true')
@slot('card_header_content')
<h4>
  Booking hari ini
</h4>
<small>
  Diambil dari 3 data teratas.
</small>
@endslot

@slot('buttons')
<a href="{{ route('my-booking-list.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Booking</a>
@endslot

@slot('table_header')
<table class="table">
  <thead>
    <tr>
      <th>#</th>
      <th>Foto</th>
      <th>Ruangan</th>
      <th>Tanggal</th>
      <th>Waktu Mulai</th>
      <th>Waktu Selesai</th>
      <th>Keperluan</th>
      <th>Status</th>
    </tr>
  </thead>
</table>
@endslot

@endcomponent

@endsection

@push('after-script')

<script>
  $(document).ready(function() {
    $('#dashboard-booking-list-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route("dashboard.booking-list") }}',
      order: [
        [3, 'asc']
      ],
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          name: 'room.photo',
          data: 'room.photo',
          orderable: false,
          searchable: false,
          render: function(data, type, row) {
            if (data != null) {
              return `<div class="gallery gallery-fw">
                <a href="{{ asset('storage/${data}') }}" data-toggle="lightbox" data-gallery="gallery-room">
                  <img src="{{ asset('storage/${data}') }}" class="img-fluid" style="min-width: 100px; height: auto;">
                </a>
              </div>`;
            } else {
              return '-'
            }
          }
        },
        {
          name: 'room.name',
          data: 'room.name',
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
          render: function(data, type, row) {
            var result = `<span class="badge badge-`;

            if (data === 'PENDING')
              result += `info`;
            else if (data === 'DISETUJUI')
              result += `primary`;
            else if (data === 'DIGUNAKAN')
              result += `primary`;
            else if (data === 'DITOLAK')
              result += `danger`;
            else if (data === 'EXPIRED')
              result += `warning`;
            else if (data === 'BATAL')
              result += `warning`;
            else if (data === 'SELESAI')
              result += `success`;

            result += `">${data}</span>`;

            return result;
          }
        },
      ],
    });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  });
</script>

@include('includes.lightbox')
@include('includes.confirm-modal')

@endpush