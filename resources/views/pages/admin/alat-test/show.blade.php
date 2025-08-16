@extends('layouts.main')

@section('title', 'Detail Alat Test - ROOMING')

@section('header-title', 'Detail Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="{{ route('alat-test.index') }}">Alat Test</a></div>
  <div class="breadcrumb-item active">Detail Alat Test</div>
@endsection

@section('section-title', 'Detail Alat Test')

@section('section-lead')
  Informasi detail mengenai alat test laboratorium.
@endsection

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>{{ $item->name }}</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          @if ($item->photo)
            <img src="{{ asset('storage/' . $item->photo) }}" class="img-fluid rounded" alt="Foto Alat Test">
          @else
            <p>Tidak ada foto tersedia.</p>
          @endif
        </div>
        <div class="col-md-8">
          <table class="table table-striped">
            <tr>
                <th>Nama Alat</th>
                <td>{{ $item->name }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $item->description }}</td>
            </tr>
            <tr>
                <th>Total Unit</th>
                <td>{{ $item->stock }}</td>
            </tr>
          </table>
          
          <br />

          <table class="table table-sm" style="margin-left: 21px;">
            <tr>
                <th>No.</th>
                <th>Serial Number</th>
                <th>Booking</th>
            </tr>
            @forelse ($item->items as $key => $unit)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $unit->serial_number }}</td>
                <td><a href="{{ route('alat-test-booking-list.index') }}">{{ $unit->bookings }}</a></td>
                {{-- <td>
                  <span class="badge badge-{{ $unit->status == 'tersedia' ? 'success' : 'warning' }}">
                    {{ ucfirst($unit->status) }}
                  </span>
                </td> --}}
              </tr>
            @empty
              <tr>
                <td colspan="3">Tidak ada unit.</td>
              </tr>
            @endforelse
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
