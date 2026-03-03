{{-- resources/views/pages/admin/plotting/import.blade.php --}}
@extends('layouts.main')

@section('title', 'Import Plotting - ROOMING')

@section('header-title', 'Import Jadwal Plotting')

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Admin</a></div>
    <div class="breadcrumb-item"><a href="{{ route('plotting.index') }}">Plotting</a></div>
    <div class="breadcrumb-item active">Import</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Upload File Excel</h4>
                <div class="card-header-form">
                    <a href="{{ route('plotting.template') }}" class="btn btn-info">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Informasi Plotting:</h5>
                    <ul class="mb-0">
                        <li>Semester: <strong>{{ $plotting->semester }} {{ $plotting->tahun_ajaran }}</strong></li>
                        <li>Periode: <strong>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</strong></li>
                    </ul>
                </div>

                <form action="{{ route('plotting.importExcel', $plotting->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label>File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" 
                               accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Format: XLSX, XLS, atau CSV. Maksimal 10MB.
                        </small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload & Proses
                        </button>
                        <a href="{{ route('plotting.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>

                <hr>

                <h5>Struktur File Excel:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>HARI</th>
                                <th>NAMA RUANGAN</th>
                                <th>JAM MULAI</th>
                                <th>JAM SELESAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Senin</td>
                                <td>Seminar 1 Lantai 3</td>
                                <td>07:20</td>
                                <td>10:10</td>
                            </tr>
                            <tr>
                                <td>Senin</td>
                                <td>Seminar 1 Lantai 3</td>
                                <td>12:20</td>
                                <td>15:10</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection