{{-- resources/views/pages/admin/plotting/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Plotting Ruangan - ROOMING')

@section('header-title', 'Plotting Ruangan')

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Admin</a></div>
    <div class="breadcrumb-item"><a href="#">Data Master</a></div>
    <div class="breadcrumb-item active">Plotting Ruangan</div>
@endsection

@section('section-title', 'Plotting Ruangan')

@section('section-lead')
    Kelola jadwal plotting ruangan untuk setiap semester.
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Daftar Plotting</h4>
                <div class="card-header-form">
                    <a href="{{ route('plotting.template') }}" class="btn btn-info mr-2">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <a href="{{ route('plotting.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Plotting Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="plotting-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Semester</th>
                                <th>Tahun Ajaran</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plottings as $index => $plotting)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $plotting->semester }}</td>
                                <td>{{ $plotting->tahun_ajaran }}</td>
                                <td>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</td>
                                <td>
                                    @if($plotting->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $plotting->creator->name ?? '-' }}</td>
                                <td>{{ $plotting->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('plotting.import', $plotting->id) }}" class="btn btn-sm btn-warning" title="Import Excel">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                    <a href="{{ route('plotting.preview', $plotting->id) }}" class="btn btn-sm btn-info" title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$plotting->is_active)
                                    <a href="{{ route('plotting.activate', $plotting->id) }}" class="btn btn-sm btn-success" onclick="return confirm('Aktifkan plotting ini?')" title="Aktifkan">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('plotting.destroy', $plotting->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus plotting ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data plotting</td>
                            </tr>
                            @endforelse
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
}

.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 15px;
    padding-top: 0;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 15px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin: 0 3px;
    border-radius: 4px;
    border: 1px solid #ddd;
    cursor: pointer;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    color: white !important;
    border-color: #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0069d9;
}

/* Clear float */
.dataTables_wrapper:after {
    content: "";
    display: table;
    clear: both;
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
    }
    
    .dataTables_wrapper .dataTables_filter input {
        display: inline-block;
        width: auto;
    }
}
</style>

<script>
$(document).ready(function() {
    $('#plotting-table').DataTable({
        "pageLength": 10,
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