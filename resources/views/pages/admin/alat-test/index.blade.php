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
			<a href="{{ route('alat-test.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Tambah Alat Test</a>
		@endslot
		
		@slot('table_id', 'alat-test-table')

		@slot('table_header')
		<tr>
			<th>#</th>
			<th>Nama Alat Test</th>
			<th>Serial Number</th>
			<th>Tipe</th>
			<th align="right">Jumlah</th>
			<th>Status</th>
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
		$('#alat-test-table').DataTable({
			processing: true,
			ajax: '{{ route('alat-test.json') }}',
			columns: [
				{ data: 'index', name: 'index' },
				{ data: 'name', name: 'name' },
				{ data: 'serial_number', name: 'serial_number' },
				{ 
					data: 'type', 
					name: 'type', 
					render: function( data, type, row) {
						if (data == 1) {
							return `Satuan`
						} else {
							return 'Lembar'
						}
					}
				},
				{ data: 'quantity', name: 'quantity' },
				{ data: 'status', name: 'status' },
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
			$('#confirm-form').attr('action', '{{ route('alat-test.destroy', ':id') }}'.replace(':id', id));
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
