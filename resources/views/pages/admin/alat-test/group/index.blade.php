@extends('layouts.main')

@section('title', 'My Booking List - ROOMING')

@section('header-title', 'Group Alat Test')

@section('breadcrumbs')
	<div class="breadcrumb-item"><a href="#">Data Master</a></div>
	<div class="breadcrumb-item">Alat Test</div>
	<div class="breadcrumb-item active">Group</div>
@endsection

@section('section-title', 'Group Alat Test')

@section('section-lead')
	Berikut ini adalah daftar seluruh data yang pernah kamu buat.
@endsection

@section('content')

	@component('components.datatables')

		@slot('buttons')
			<a href="{{ route('alat-test.group.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Buat Baru</a>
		@endslot

		@slot('table_id', 'alat-test-group-table')

		@slot('table_header')
			<tr>
				<th>#</th>
				<th>Foto</th>
				<th>Nama</th>
				<th>Stok</th>
				<th>Keterangan</th>
				<th>Aksi</th>
			</tr>
		@endslot
		
	@endcomponent

@endsection

@push('after-script')
<script src="//cdn.datatables.net/plug-ins/1.10.22/dataRender/ellipsis.js"></script>

<script>
	$(document).ready(function() {

		$('#alat-test-group-table').DataTable({
			processing: true,
			ajax: '{{ route('alat-test.group.list') }}',
			columns: [
				{
					name: 'index',
					data: 'index',
					orderable: false, 
					searchable: false,
				},
				{
					name: 'photo',
					data: 'photo',
					orderable: false, 
					searchable: false,
					render: function ( data, type, row ) {
						if(data != null) {
						return `<div class="gallery gallery-fw">`
							+ `<a href="{{ asset('storage/${data}') }}" data-toggle="lightbox">`
							+ `<img src="{{ asset('storage/${data}') }}" class="img-fluid" style="min-width: 80px; height: auto;">`
							+ `</a>`
						+ '</div>';
						} else {
						return '-'
						}
					}
				},
				{
					name: 'name',
					data: 'name',
				},
				{
					name: 'items_sum',
					data: 'items_sum',
				},
				{
					name: 'description',
					data: 'description',
				},
				{
					data: 'id',
					name: 'aksi',
					orderable: false,
					searchable: false,
					render: function(id) {
						return `
						<div class="table-links">
							<a href="group/${id}/show" class="text-info">Detail</a>
							<div class="bullet"></div>
							<a href="group/${id}/edit" class="text-primary">Edit</a>
							<div class="bullet"></div>
							<a href="javascript:;" data-id="${id}" data-title="Hapus" data-body="Yakin ingin menghapus ini?" class="text-danger" id="delete-btn">Hapus</a>
						</div>`;
					}
				}
			],
		});

		$(document).on('click', '#delete-btn', function() {
			var id = $(this).data('id');
			var title = $(this).data('title');
			var body = $(this).data('body');

			$('.modal-title').html(title);
			$('.modal-body').html(body);
			$('#confirm-form').attr('action', '{{ route('alat-test.group.destroy', ':id') }}'.replace(':id', id));
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