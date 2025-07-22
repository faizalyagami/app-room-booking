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
      <a href="{{ route('user.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>&nbsp;Tambah User
      </a>
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

@push('after-script')

<script>
  $(document).ready(function () {
    $('#user-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('user.json') }}',
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
  });
</script>

@include('includes.notification')
@include('includes.confirm-modal')

@endpush
