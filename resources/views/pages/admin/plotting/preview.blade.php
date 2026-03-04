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
                                <td>: <strong>{{ $plotting->semester }} {{ $plotting->tahun_ajaran }}</strong></td>
                            </tr>
                            <tr>
                                <th>Periode</th>
                                <td>: <strong>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="30%">Dibuat Oleh</th>
                                <td>: <strong>{{ $plotting->creator->name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Total Jadwal</th>
                                <td>: <strong>{{ $bookings->flatten()->count() }} slot</strong></td>
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
                                        <span class="badge badge-info">{{ $days[$dayNum] }}</span>
                                    </td>
                                    <td>{{ $booking->room->name }}</td>
                                    <td><strong>{{ substr($booking->start_time, 0, 5) }}</strong></td>
                                    <td><strong>{{ substr($booking->end_time, 0, 5) }}</strong></td>
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
<link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

<style>

.dataTables_wrapper .dataTables_length {
    float: left;
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 15px;
    text-align: right;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 10px;
    border-radius: 4px;
    padding: 5px 10px;
    border: 1px solid #ddd;
    width: 200px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 15px;
    padding-top: 0;
    color: #666;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 15px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 3px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    cursor: pointer;
    color: #007bff;
    background-color: #fff;
    display: inline-block;
    font-size: 14px;
    transition: all 0.3s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    color: white !important;
    border-color: #007bff;
    z-index: 3;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    color: #0056b3 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0069d9;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #6c757d !important;
    background-color: #fff;
    border-color: #dee2e6;
    opacity: 0.65;
    cursor: not-allowed;
    pointer-events: none;
}

/* Clear float */
.dataTables_wrapper:after {
    content: "";
    display: table;
    clear: both;
}

/* Styling untuk select entries */
.dataTables_wrapper .dataTables_length select {
    padding: 5px 10px;
    border-radius: 4px;
    border: 1px solid #ddd;
    margin: 0 5px;
}

.dataTables_wrapper .dataTables_length select:focus {
    outline: none;
    border-color: #007bff;
}

/* Responsive untuk mobile */
@media screen and (max-width: 767px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: none;
        text-align: center;
        width: 100%;
        margin-top: 10px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        display: inline-block;
        width: 80%;
        max-width: 300px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 4px 8px;
        margin: 2px;
    }
}

/* Styling untuk badge hari */
.badge-info {
    background-color: #17a2b8;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 500;
}
</style>

<script>
$(document).ready(function() {
    $('#preview-table').DataTable({
        "pageLength": 10,
        "order": [[1, 'asc'], [0, 'asc']],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "zeroRecords": "Tidak ada data yang cocok",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
        },
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });
});
</script>
@endpush