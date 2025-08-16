@extends('layouts.main')

@section('title')
    Detail Data Ruangan - ROOMING
@endsection 

@section('header-title')
    Detail Data Ruangan {{ $item->name }}
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Ruangan</a></div>
  <div class="breadcrumb-item"><a href="{{ route('room.index') }}">Data Ruangan</a></div>
  <div class="breadcrumb-item @if(isset($item)) '' @else 'active' @endif">
    @if(isset($item))
      <a href="#">Edit Data Ruangan</a>
    @else 
      Tambah Data Ruangan 
    @endif
  </div>
  @isset($item)
    <div class="breadcrumb-item active">{{ $item->name }}</div>
  @endisset
@endsection

@section('section-title')
  Detail Data Ruangan {{ $item->name }}
@endsection 
    
@section('section-lead')
  {{ $item->name }}
@endsection

@section('content')

<div class="row">
  <div class="col ">
    <div class="card">

      <table class="table">
        <tbody>
          <tr>
            <th style="width: 10%;">Nama</th>
            <td>{{ $item->name }}</td>
          </tr>
          <tr>
            <th scope="row">Kapasitas</th>
            <td>{{ $item->capacity }}</td>
          </tr>
          <tr>
            <th scope="row">Deskripsi</th>
            <td>{{ $item->description }}</td>
          </tr>
          <tr>
            <td colspan="2">
    
            </td>
          </tr>
        </tbody>
      </table>

      <h5 class="card-title">Data Booking</h5>
      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Jam</th>
            <th scope="col">Tujuan</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @if(count($bookings))
            @foreach ($bookings as $booking)
                <tr>
                  <td>{{ $booking->user->name }}</td>
                  <td>{{ date("d F Y", strtotime($booking->date)) }}</td>
                  <td>{{ date("H:i", strtotime($booking->start_time)) }} - {{ date("H:i", strtotime($booking->end_time)) }}</td>
                  <td>{{ $booking->purpose }}</td>
                  <td>{{ $booking->status }}</td>
                </tr>
            @endforeach
          @endif
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection
