@extends('layouts.main')

@section('title', 'Data User - ROOMING')
@section('header-title', 'Data User')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">User</a></div>
  <div class="breadcrumb-item active">Data User</div>
@endsection

@section('section-title', 'User')

@section('section-lead')
  Berikut ini adalah daftar seluruh user yang ada.
@endsection

@section('content')

  @component('components.datatables')
    @slot('buttons')
      <div class="dropdown d-inline">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownTambahUser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-plus"></i>&nbsp;Tambah User
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownTambahUser">
          <a class="dropdown-item" href="{{ route('user.create') }}">
            <i class="fas fa-user-plus text-primary mr-2"></i> Tambah Manual
          </a>
          <a class="dropdown-item" href="{{ route('user.downloadTemplate') }}">
            <i class="fas fa-download text-success mr-2"></i> Unduh Format
          </a>
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-import text-warning mr-2"></i> Import
          </a>
        </div>
      </div>
    @endslot

    @slot('table_id', 'user-table')

    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Email</th>
        <th>Username</th>
        <th>NPM</th>
        <th>Nama</th>
      </tr>
    @endslot

  @endcomponent

@endsection

{{-- MODAL IMPORT --}}
@push('modal-box')
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="importForm" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Users from Excel</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="file">Pilih File Excel</label>
            <input type="file" name="file" id="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
          </div>

          {{-- Progress Bar --}}
          <div class="progress mt-3" style="height: 25px; display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                 role="progressbar" style="width: 0%">0%</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Import</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endpush

@push('after-script')
@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: "{{ session('success') }}",
    timer: 3000,
    showConfirmButton: false
  });
</script>
@endif

<script>
  $(document).ready(function () {
    // Inisialisasi DataTable
    let table = $('#user-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('user.json') }}",
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'email', name: 'email' },
        { data: 'username', name: 'username' },
        { data: 'npm', name: 'npm' },
        {
          data: 'name',
          name: 'name',
          render: function (data, type, row) {
            let result = row.name;
            const isTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;
            result += isTouch ? '<div>' : '<div class="table-links">';
            result += `
              <a href="user/${row.id}/edit" class="text-primary">Edit</a>
              <div class="bullet"></div>
              <a href="user/${row.id}/change-pass" class="text-primary">Ganti Password</a>
              <div class="bullet"></div>
              <a href="javascript:;" class="text-danger delete-user-btn"
                 data-id="${row.id}"
                 data-title="Hapus"
                 data-body="Yakin ingin menghapus user ini?">Hapus</a>
            </div>`;
            return result;
          }
        },
      ],
      order: [[1, 'asc']],
    });

    // Reset progress bar setiap modal dibuka
    $('#importModal').on('show.bs.modal', function () {
      $('.progress').hide();
      $('.progress-bar').css('width', '0%').text('0%').removeClass('bg-success bg-danger').addClass('bg-info');
      $('#file').val('');
    });

    // Handle Delete
    $(document).on('click', '.delete-user-btn', function () {
      const id = $(this).data('id');
      const title = $(this).data('title');
      const body = $(this).data('body');

      $('.modal-title').text(title);
      $('.modal-body').text(body);
      $('#confirm-form').attr('action', `user/${id}`);
      $('#confirm-form').attr('method', 'POST');
      $('#lara-method').val('delete');
      $('#submit-btn').attr('class', 'btn btn-danger');
      $('#confirm-modal').modal('show');
    });

    // Handle Import dengan Progress Bar
    $('#importForm').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const progressBar = $('.progress');
      const progressValue = $('.progress-bar');

      progressBar.show();
      progressValue.css('width', '0%').text('0%')
                   .removeClass('bg-success bg-danger')
                   .addClass('bg-info');

      $.ajax({
        xhr: function() {
          let xhr = new XMLHttpRequest();
          xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
              let percent = Math.round((e.loaded / e.total) * 100);
              progressValue.css('width', percent + '%').text(percent + '%');
            }
          });
          return xhr;
        },
        type: 'POST',
        url: "{{ route('user.importExcel') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          progressValue.css('width', '100%').text('100%')
                       .removeClass('bg-info')
                       .addClass('bg-success')
                       .text('Upload Selesai!');
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data user berhasil diimport!',
            timer: 2000,
            showConfirmButton: false
          });
          setTimeout(() => {
            $('#importModal').modal('hide');
            table.ajax.reload(); // reload datatable tanpa reload halaman
          }, 1000);
        },
        error: function(xhr) {
          progressValue.removeClass('bg-info').addClass('bg-danger').text('Gagal!');
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Import data gagal. Periksa file Anda.',
          });
        }
      });
    });
  });
</script>

@include('includes.notification')
@include('includes.confirm-modal')
@endpush
