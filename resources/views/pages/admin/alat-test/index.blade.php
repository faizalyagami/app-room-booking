@extends('layouts.main')

@section('title', 'Data Alat Test - ROOMING')

@section('header-title', 'Data Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Alat Test</a></div>
  <div class="breadcrumb-item active">Data Alat Test</div>
@endsection

@section('section-title', 'Alat Test')

@section('section-lead')
  Berikut ini adalah daftar seluruh alat test yang tersedia di laboratorium.
@endsection

@section('content')

  @component('components.datatables')
    @slot('buttons')
      <a href="{{ route('alat-test-admin.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Tambah Alat Test</a>
    @endslot
    
    @slot('table_id', 'alat-table')

    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Foto</th>
        <th>Nama Alat Test</th>
        <th>Deskripsi</th>
        <th>Stok</th>
        <th>Aksi</th>
      </tr>
    @endslot
  @endcomponent

@endsection

@push('after-script')
<style>
  .text-wrap {
    white-space: normal !important;
    word-wrap: break-word;
  }
</style>
<script>
  $(document).ready(function() {
    $('#booking_alats').DataTable({
      processing: true,
      ajax: '{{ route('alat-test.json') }}',
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        {
          data: 'photo',
          name: 'photo',
          render: function(data) {
            if (data) {
              return `
                <div class="gallery gallery-fw">
                  <a href="/storage/${data}" data-toggle="lightbox">
                    <img src="/storage/${data}" class="img-fluid" style="min-width: 80px; height: auto;">
                  </a>
                </div>`;
            } else {
              return '-';
            }
          }
        },
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description', className: 'text-wrap' },
        { data: 'stock', name: 'stock' },
        {
          data: 'id',
          name: 'aksi',
          orderable: false,
          searchable: false,
          render: function(id) {
            return `
              <div class="table-links">
                <a href="alat-test/${id}" class="text-info">Detail</a>
                <div class="bullet"></div>
                <a href="alat-test/${id}/edit" class="text-primary">Edit</a>
                <div class="bullet"></div>
                <a href="javascript:;" data-id="${id}" data-title="Hapus" data-body="Yakin ingin menghapus ini?" class="text-danger" id="delete-btn">Hapus</a>
              </div>`;
          }
        }
      ]
    });


    $(document).on('click', '#delete-btn', function() {
      var id = $(this).data('id');
      var title = $(this).data('title');
      var body = $(this).data('body');

      $('.modal-title').html(title);
      $('.modal-body').html(body);
      $('#confirm-form').attr('action', '{{ route('alat-test-admin.destroy', ':id') }}'.replace(':id', id));
      $('#confirm-form').attr('method', 'POST');
      $('#submit-btn').attr('class', 'btn btn-danger');
      $('#lara-method').attr('value', 'delete');
      $('#confirm-modal').modal('show');
    });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  });
</script>

@include('includes.lightbox')
@include('includes.notification')
@include('includes.confirm-modal')
@endpush
