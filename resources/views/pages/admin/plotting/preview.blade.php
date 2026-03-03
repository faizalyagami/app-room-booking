{{-- resources/views/pages/admin/plotting/preview.blade.php --}}
@extends('layouts.main')

@section('title', 'Preview Plotting - ROOMING')

@section('header-title', 'Preview Plotting')

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Admin</a></div>
    <div class="breadcrumb-item"><a href="{{ route('plotting.index') }}">Plotting</a></div>
    <div class="breadcrumb-item active">Preview</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Detail Plotting</h4>
                <div class="card-header-form">
                    <a href="{{ route('plotting.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="30%">Semester</th>
                                <td>: {{ $plotting->semester }} {{ $plotting->tahun_ajaran }}</td>
                            </tr>
                            <tr>
                                <th>Periode</th>
                                <td>: {{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="30%">Dibuat Oleh</th>
                                <td>: {{ $plotting->creator->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Total Jadwal</th>
                                <td>: {{ $bookings->flatten()->count() }} slot</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h5>Daftar Jadwal Plotting</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="preview-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Ruangan</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($bookings as $date => $items)
                                @foreach($items as $booking)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            $dayNum = \Carbon\Carbon::parse($date)->dayOfWeek;
                                        @endphp
                                        {{ $days[$dayNum] }}
                                    </td>
                                    <td>{{ $booking->room->name }}</td>
                                    <td>{{ substr($booking->start_time, 0, 5) }}</td>
                                    <td>{{ substr($booking->end_time, 0, 5) }}</td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#preview-table').DataTable({
        "pageLength": 25,
        "order": [[1, 'asc'], [4, 'asc']]
    });
});
</script>
@endpush