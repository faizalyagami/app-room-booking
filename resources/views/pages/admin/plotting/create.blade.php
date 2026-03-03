{{-- resources/views/pages/admin/plotting/create.blade.php --}}
@extends('layouts.main')

@section('title', 'Buat Plotting Baru - ROOMING')

@section('header-title', 'Buat Plotting Baru')

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">Admin</a></div>
    <div class="breadcrumb-item"><a href="{{ route('plotting.index') }}">Plotting</a></div>
    <div class="breadcrumb-item active">Buat Baru</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Form Plotting Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('plotting.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Semester</label>
                        <div class="col-sm-9">
                            <select name="semester" class="form-control @error('semester') is-invalid @enderror" required>
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tahun Ajaran</label>
                        <div class="col-sm-9">
                            <input type="text" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                                   placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajaran', '2025/2026') }}" required>
                            @error('tahun_ajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tanggal Mulai</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                   value="{{ old('tanggal_mulai', '2026-03-01') }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tanggal Selesai</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   value="{{ old('tanggal_selesai', '2026-07-10') }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">Buat Plotting</button>
                            <a href="{{ route('plotting.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection