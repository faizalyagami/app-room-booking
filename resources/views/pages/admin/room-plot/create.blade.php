@extends('layouts.main')

@section('title', 'Tambah Plot Ruangan - ROOMING')

@section('header-title', 'Tambah Plot Ruangan')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item"><a href="{{ route('admin.room-plot.index') }}">Plot Ruangan</a></div>
<div class="breadcrumb-item active">Tambah Plot</div>
@endsection

@section('section-title', 'Tambah Plot Ruangan')
@section('section-lead', 'Upload gambar plot ruangan untuk ditampilkan ke user.')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.room-plot.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Pilih Ruangan</label>
                        <select name="room_id" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('room_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Gambar Plot Ruangan</label>
                        <input type="file" name="plot_image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Upload gambar plot ruangan (Max: 5MB, Format: JPG, PNG, GIF)</small>
                        @error('plot_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Plot</label>
                        <textarea name="plot_description" class="form-control" rows="4">{{ old('plot_description') }}</textarea>
                        <small class="text-muted">Deskripsi singkat tentang plot ruangan (opsional)</small>
                        @error('plot_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Mulai</label>
                                <input type="date" name="plot_valid_from" class="form-control" value="{{ old('plot_valid_from') }}">
                                <small class="text-muted">Kosongkan jika selalu berlaku</small>
                                @error('plot_valid_from')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Sampai</label>
                                <input type="date" name="plot_valid_until" class="form-control" value="{{ old('plot_valid_until') }}">
                                <small class="text-muted">Kosongkan jika selalu berlaku</small>
                                @error('plot_valid_until')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Plot</button>
                        <a href="{{ route('admin.room-plot.index') }}" class="btn btn-secondary">Batal</a>
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
                <p>Gambar plot ruangan akan ditampilkan di dashboard user untuk memberi informasi tentang:</p>
                <ul>
                    <li>Jadwal tetap ruangan</li>
                    <li>Penggunaan khusus ruangan</li>
                    <li>Informasi penting lainnya</li>
                </ul>
                <p class="text-muted mt-3">
                    <small>Jika masa berlaku dikosongkan, plot akan dianggap selalu berlaku.</small>
                </p>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <small>Gambar akan ditampilkan di halaman dashboard user.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection