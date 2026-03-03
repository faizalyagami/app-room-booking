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
                    <a href="{{ route('plotting.template') }}" class="btn btn-info mr-2"> {{-- PERBAIKAN --}}
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
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
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
                                    <a href="{{ route('plotting.preview', $plotting->id) }}" class="btn btn-sm btn-info" title="Preview"> {{-- PERBAIKAN --}}
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$plotting->is_active)
                                    <a href="{{ route('plotting.activate', $plotting->id) }}" class="btn btn-sm btn-success" onclick="return confirm('Aktifkan plotting ini?')" title="Aktifkan"> {{-- PERBAIKAN --}}
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endif
                                    
                                    <form action="{{ route('plotting.destroy', $plotting->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus plotting ini?')"> {{-- PERBAIKAN --}}
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
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#plotting-table').DataTable({
        "pageLength": 10,
        "ordering": true
    });
});
</script>
@endpush