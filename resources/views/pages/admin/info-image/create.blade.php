@extends('layouts.main')

@section('title', 'Tambah Info Gambar - ROOMING')

@section('header-title', 'Tambah Info Gambar')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item"><a href="{{ route('admin.info-image.index') }}">Info Gambar</a></div>
<div class="breadcrumb-item active">Tambah Gambar</div>
@endsection

@section('section-title', 'Tambah Info Gambar')
@section('section-lead', 'Upload gambar informasi untuk ditampilkan ke user di dashboard.')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.info-image.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Judul <span class="text-muted">(Opsional)</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Jadwal Praktikum 2025">
                        @error('title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Gambar Info <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Upload gambar (Max: 5MB, Format: JPG, PNG, GIF)</small>
                        @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Deskripsi <span class="text-muted">(Opsional)</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang gambar ini">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Mulai</label>
                                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                                <small class="text-muted">Kosongkan jika selalu berlaku</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Sampai</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                                <small class="text-muted">Kosongkan jika selalu berlaku</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        <small class="text-muted">Semakin kecil angka, semakin atas tampilannya</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.info-image.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Informasi</h4>
            </div>
            <div class="card-body">
                <p>Gambar info akan ditampilkan di dashboard user untuk memberikan informasi seperti:</p>
                <ul>
                    <li>Jadwal tetap ruangan/lab</li>
                    <li>Pengumuman penting</li>
                    <li>Jadwal praktikum</li>
                    <li>Informasi lainnya</li>
                </ul>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <small>Gambar akan ditampilkan secara otomatis di halaman dashboard user.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection