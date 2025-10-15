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
            <img src="{{ asset('storage/' . $item->alatTest->photo) }}" class="img-fluid rounded" alt="Foto Alat Test">
          @else
            <p>Tidak ada foto tersedia.</p>
          @endif
        </div>
        <div class="col-md-8">
          <table class="table table-striped">
            <tr>
                <th>Nama Alat</th>
                <td>{{ $item->alatTest->name }}</td>
            </tr>
            <tr>
                <th>Serial Number</th>
                <td>{{ $item->serial_number }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $item->alatTest->description }}</td>
            </tr>
            <tr>
                <th>Total Unit</th>
                <td>{{ $item->quantity }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Logs</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped">
          <tr>
              <th>No.</th>
              <th>Tanggal</th>
              <th>Tipe</th>
              <th>Jumlah</th>
          </tr>

          @if(count($logs) > 0)
            @foreach ($logs as $key => $log)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ date("d M Y", strtotime($log->date)) }}</td>
                <td>
                  @if($log->type == 'Masuk')
                    <span style="color: green;">{{ $log->type }}</span>
                  @else
                    <span style="color: red;">{{ $log->type }}</span>
                  @endif
                </td>
                <td>{{ $log->quantity }}</td>
              </tr>
            @endforeach
          @endif
        </table>
      </div>
    </div>
  </div>
@endsection
