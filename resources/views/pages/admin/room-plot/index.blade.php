@extends('layouts.main')

@section('title', 'Plot Ruangan - ROOMING')

@section('header-title', 'Plot Ruangan')

@section('breadcrumbs')
<div class="breadcrumb-item"><a href="#">Admin</a></div>
<div class="breadcrumb-item active">Plot Ruangan</div>
@endsection

@section('section-title', 'Plot Ruangan')
@section('section-lead', 'Atur gambar plot ruangan yang akan ditampilkan ke user.')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-map-marked-alt"></i> Daftar Plot Ruangan</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.room-plot.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Plot
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="room-plot-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Ruangan</th>
                        <th>Gambar Plot</th>
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
    function deletePlot(id) {
        if (confirm('Yakin hapus plot ruangan ini?')) {
            $.ajax({
                url: '/admin/room-plot/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#room-plot-table').DataTable().ajax.reload();
                    alert('Plot berhasil dihapus');
                }
            });
        }
    }

    $(document).ready(function() {
        $('#room-plot-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.room-plot.json") }}',
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'plot_image_preview',
                    name: 'plot_image_preview',
                    orderable: false
                },
                {
                    data: 'plot_description',
                    name: 'plot_description'
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