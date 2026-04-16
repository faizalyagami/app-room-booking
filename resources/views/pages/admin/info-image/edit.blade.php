@extends('layouts.main')

@section('title', 'Edit Info Gambar - ROOMING')

@section('header-title', 'Edit Info Gambar')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item"><a href="{{ route('admin.info-image.index') }}">Info Gambar</a></div>
<div class="breadcrumb-item active">Edit Gambar</div>
@endsection

@section('section-title', 'Edit Info Gambar')
@section('section-lead', 'Edit gambar informasi yang akan ditampilkan ke user.')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.info-image.update', $infoImage->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Judul <span class="text-muted">(Opsional)</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $infoImage->title) }}">
                    </div>

                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $infoImage->image) }}" class="img-fluid" style="max-height: 200px;">
                        </div>
                        <label>Ganti Gambar <span class="text-muted">(Opsional)</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload gambar baru jika ingin mengganti (Max: 5MB)</small>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi <span class="text-muted">(Opsional)</span></label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $infoImage->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Mulai</label>
                                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', $infoImage->valid_from) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berlaku Sampai</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $infoImage->valid_until) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $infoImage->sort_order) }}">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" {{ $infoImage->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Aktifkan</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
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
                <p>Gambar info akan ditampilkan di dashboard user.</p>
                <p class="text-muted mt-3">
                    <small>Jika masa berlaku dikosongkan, gambar akan dianggap selalu berlaku.</small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection