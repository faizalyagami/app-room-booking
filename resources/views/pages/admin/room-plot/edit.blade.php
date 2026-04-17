@extends('layouts.main')

@section('title', 'Edit Plot Ruangan - ROOMING')

@section('header-title', 'Edit Plot Ruangan')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item"><a href="{{ route('admin.room-plot.index') }}">Plot Ruangan</a></div>
<div class="breadcrumb-item active">Edit Plot</div>
@endsection

@section('section-title', 'Edit Plot Ruangan: ' . $room->name)
@section('section-lead', 'Upload gambar plot ruangan untuk ditampilkan ke user.')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.room-plot.update', $room->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Gambar Plot Ruangan</label>
                        @if($room->plot_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $room->plot_image) }}" class="img-fluid" style="max-height: 300px;">
                        </div>
                        @endif
                        <input type="file" name="plot_image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload gambar plot ruangan (Max: 5MB)</small>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Plot</label>
                        <textarea name="plot_description" class="form-control" rows="4">{{ old('plot_description', $room->plot_description) }}</textarea>
                        <small class="text-muted">Deskripsi singkat tentang plot ruangan</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Mulai</label>
                                <input type="date" name="plot_valid_from" class="form-control" value="{{ old('plot_valid_from', $room->plot_valid_from) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Sampai</label>
                                <input type="date" name="plot_valid_until" class="form-control" value="{{ old('plot_valid_until', $room->plot_valid_until) }}">
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
            </div>
        </div>
    </div>
</div>
@endsection