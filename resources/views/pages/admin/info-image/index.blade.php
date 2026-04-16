@extends('layouts.main')

@section('title', 'Info Gambar - ROOMING')

@section('header-title', 'Info Gambar')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item active">Info Gambar</div>
@endsection

@section('section-title', 'Info Gambar')
@section('section-lead', 'Upload gambar informasi yang akan ditampilkan di dashboard user.')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-images"></i> Daftar Info Gambar</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.info-image.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Gambar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="info-image-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Masa Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteImage(id) {
        if (confirm('Yakin hapus gambar ini?')) {
            $.ajax({
                url: '/admin/info-image/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#info-image-table').DataTable().ajax.reload();
                    alert('Gambar berhasil dihapus');
                }
            });
        }
    }

    $(document).ready(function() {
        $('#info-image-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.info-image.json") }}',
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'image_preview',
                    name: 'image_preview',
                    orderable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'validity',
                    name: 'validity'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                }
            ]
        });
    });
</script>
@endsection