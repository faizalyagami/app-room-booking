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
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            {!! session('error') !!}
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Informasi Plotting:</h5>
                    <ul class="mb-0">
                        <li>Semester: <strong>{{ $plotting->semester }} {{ $plotting->tahun_ajaran }}</strong></li>
                        <li>Periode: <strong>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</strong></li>
                        @if($plotting->data_plotting)
                            <li>Total Pola Jadwal: <strong>{{ count($plotting->data_plotting) }} pola</strong></li>
                            <li>Total Hari: <strong>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($plotting->tanggal_selesai)) + 1 }} hari</strong></li>
                        @endif
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

                {{-- CONTOH STRUKTUR FILE EXCEL (BUKAN HASIL IMPORT) --}}
                <div class="alert alert-warning">
                    <h5><i class="fas fa-file-excel"></i> Contoh Struktur File Excel:</h5>
                    <p class="mb-0">File Excel harus memiliki 4 kolom dengan urutan: <strong>HARI, NAMA RUANGAN, JAM MULAI, JAM SELESAI</strong></p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="20%">HARI</th>
                                <th width="40%">NAMA RUANGAN</th>
                                <th width="20%">JAM MULAI</th>
                                <th width="20%">JAM SELESAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-light">
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fas fa-arrow-up"></i> Ini adalah CONTOH format, BUKAN data yang sudah diimport <i class="fas fa-arrow-up"></i>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Senin</span></td>
                                <td>Seminar 1 Lantai 3</td>
                                <td><code>07:20</code></td>
                                <td><code>10:10</code></td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Senin</span></td>
                                <td>Seminar 1 Lantai 3</td>
                                <td><code>12:20</code></td>
                                <td><code>15:10</code></td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Selasa</span></td>
                                <td>Lab Psikologi 15 Lantai 4</td>
                                <td><code>07:20</code></td>
                                <td><code>10:10</code></td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Selasa</span></td>
                                <td>Lab Psikologi 15 Lantai 4</td>
                                <td><code>12:20</code></td>
                                <td><code>15:10</code></td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Rabu</span></td>
                                <td>Ruang Tes Anak</td>
                                <td><code>07:20</code></td>
                                <td><code>10:10</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TAMPILAN HASIL IMPORT (HANYA MUNCUL JIKA SUDAH PERNAH IMPORT) --}}
                @if($plotting->data_plotting && count($plotting->data_plotting) > 0)
                <div class="mt-4">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Data Import Sebelumnya:</h5>
                        <p class="mb-0">Berikut adalah pola jadwal yang sudah pernah diimport:</p>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="hasil-import-table">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">HARI</th>
                                    <th width="45%">NAMA RUANGAN</th>
                                    <th width="15%">JAM MULAI</th>
                                    <th width="15%">JAM SELESAI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plotting->data_plotting as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $hariMap = [
                                                'senin' => 'Senin',
                                                'selasa' => 'Selasa',
                                                'rabu' => 'Rabu',
                                                'kamis' => 'Kamis',
                                                'jumat' => 'Jumat',
                                                'sabtu' => 'Sabtu',
                                                'minggu' => 'Minggu'
                                            ];
                                            $hariDisplay = $hariMap[strtolower($item['hari'])] ?? ucfirst($item['hari']);
                                        @endphp
                                        <span class="badge badge-info">{{ $hariDisplay }}</span>
                                    </td>
                                    <td>{{ $item['room_name'] }}</td>
                                    <td><code>{{ substr($item['start_time'], 0, 5) }}</code></td>
                                    <td><code>{{ substr($item['end_time'], 0, 5) }}</code></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> 
                        <strong>{{ count($plotting->data_plotting) }} pola jadwal</strong> akan digenerate otomatis untuk setiap hari yang sama selama periode 
                        <strong>{{ \Carbon\Carbon::parse($plotting->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plotting->tanggal_selesai)->format('d/m/Y') }}</strong>
                    </div>
                </div>
                @else
                {{-- TAMPILAN KETIKA BELUM PERNAH IMPORT --}}
                <div class="mt-4">
                    <div class="alert alert-secondary">
                        <h5><i class="fas fa-info-circle"></i> Belum Ada Data Import</h5>
                        <p class="mb-0">Silahkan upload file Excel untuk memulai import jadwal plotting.</p>
                    </div>
                </div>
                @endif

                {{-- INFORMASI TAMBAHAN --}}
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting:</h6>
                        <ul class="mb-0">
                            <li>Nama ruangan harus <strong>sama persis</strong> dengan yang ada di database</li>
                            <li>Format jam: <strong>HH:MM</strong> atau <strong>HH.MM</strong> (contoh: 07:20 atau 07.20)</li>
                            <li>Hari: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu</li>
                            <li>File akan diproses per baris dan digenerate otomatis untuk setiap tanggal di periode yang sama</li>
                            <li>Jika sudah pernah import, data lama akan <strong>terhapus</strong> dan diganti dengan yang baru</li>
                        </ul>
                    </div>
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
    // Inisialisasi DataTable untuk tabel hasil import (jika ada)
    if ($('#hasil-import-table').length > 0) {
        $('#hasil-import-table').DataTable({
            "pageLength": 10,
            "ordering": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
            }
        });
    }
});
</script>
@endpush