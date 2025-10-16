@extends('layouts.main')

@section('title', 'Dashboard - ROOMING')

@section('header-title', 'Dashboard')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
  <div class="breadcrumb-item active">Dashboard</div>
@endsection
    
@section('content')
<div class="row">

  <div class="col-lg-6 col-md-6 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-stats">
        <div class="card-stats-title">Statistik Booking
          
        </div>
        <div class="card-stats-items">
          <div class="card-stats-item">
            <div class="card-stats-item-count @if($booking_list_pending > 0) {{ 'text-info' }} @endif">{{ $booking_list_pending }}</div>
            <div class="card-stats-item-label">Pending</div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_disetujui }}</div>
            <div class="card-stats-item-label">Disetujui</div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_digunakan }}</div>
            <div class="card-stats-item-label">Sedang Digunakan</div>
          </div>
        </div>
        <div class="card-stats-items">
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_selesai }}</div>
            <div class="card-stats-item-label">Selesai</div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_batal }}</div>
            <div class="card-stats-item-label">Batal</div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_ditolak }}</div>
            <div class="card-stats-item-label">Ditolak</div>
          </div>
        </div>
        <div class="card-stats-items justify-content-center">
          <div class="card-stats-item">
            <div class="card-stats-item-count">{{ $booking_list_expired }}</div>
            <div class="card-stats-item-label">Expired</div>
          </div>
        </div>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-list"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Permintaan Booking</h4>
        </div>
        <div class="card-body">
          {{ $booking_list_all }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12">
  <div class="card card-statistic-2">
    <div class="card-stats">
      <div class="card-stats-title">Statistik Peminjaman Alat Test</div>
      <div class="card-stats-items">
        <div class="card-stats-item">
          <div class="card-stats-item-count text-warning">{{ $alat_test_pending }}</div>
          <div class="card-stats-item-label">Pending</div>
        </div>
        <div class="card-stats-item">
          <div class="card-stats-item-count text-info">{{ $alat_test_disetujui }}</div>
          <div class="card-stats-item-label">Disetujui</div>
        </div>
        <div class="card-stats-item">
          <div class="card-stats-item-count text-success">{{ $alat_test_dikembalikan }}</div>
          <div class="card-stats-item-label">Dikembalikan</div>
        </div>
      </div>
      <div class="card-stats-items justify-content-center">
        <div class="card-stats-item">
          <div class="card-stats-item-count text-danger">{{ $alat_test_ditolak }}</div>
          <div class="card-stats-item-label">Ditolak</div>
        </div>
      </div>
    </div>
    <div class="card-icon shadow-primary bg-info">
      <i class="fas fa-toolbox"></i>
    </div>
    <div class="card-wrap">
      <div class="card-header">
        <h4>Total Peminjaman Alat</h4>
      </div>
      <div class="card-body">
        {{ $alat_test_all }}
      </div>
    </div>
  </div>
</div>


  <div class="col-lg-3 col-md-6 col-sm-6 col-12">    
    @component('components.statistic-card')
      @slot('bg_color', 'bg-primary')
      @slot('icon', 'fas fa-door-open')
      @slot('title', 'Total Ruangan')
      @slot('value', $room)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
      @slot('bg_color', 'bg-primary')
      @slot('icon', 'fas fa-user')
      @slot('title', 'Jumlah Mahasiswa')
      @slot('value', $user)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
      @slot('bg_color', 'bg-info')
      @slot('icon', 'fas fa-toolbox')
      @slot('title', 'Total Alat Test')
      @slot('value', $alat_test)
    @endcomponent
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    @component('components.statistic-card')
      @slot('bg_color', 'bg-info')
      @slot('icon', 'fas fa-user')
      @slot('title', 'Jumlah Mahasiswa')
      @slot('value', $user)
    @endcomponent
  </div>
@endsection
