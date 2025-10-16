@extends('layouts.main')

@section('title', 'Log Alat Test - ROOMING')

@section('header-title', 'Log Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="{{ route('alat-test.index') }}">Alat Test</a></div>
  <div class="breadcrumb-item active">Log Alat Test</div>
@endsection

@section('section-title', 'Log Alat Test')

@section('section-lead')
  Informasi detail mengenai alat test laboratorium.
@endsection

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Logs</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped">
          <tr>
              <th>No.</th>
              <th>Nama</th>
              <th>Serial Number</th>
              <th>Tanggal</th>
              <th>Tipe</th>
              <th>Jumlah</th>
          </tr>

          @if(count($logs) > 0)
            @foreach ($logs as $key => $log)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->alatTest->name }}</td>
                <td>{{ $item->serial_number }}</td>
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
