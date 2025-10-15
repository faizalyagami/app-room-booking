@extends('layouts.main')

@section('title')
	Update Data group Alat Test - ROOMING
@endsection

@section('header-title')
	Update Data group Alat Test
@endsection

@section('breadcrumbs')
	<div class="breadcrumb-item"><a href="#">Data Master</a></div>
	<div class="breadcrumb-item">Alat Test</div>
	<div class="breadcrumb-item">Group</div>
	<div class="breadcrumb-item active">Update Data</div>
@endsection

@section('section-title')
	Update Data Group Alat Test
@endsection

@section('section-lead')
	Silakan isi form di bawah ini untuk menambah data Group Alat Test.
@endsection

@section('content')
	@component('components.form')
		@slot('row_class', '')
		@slot('col_class', 'col-12')

		@slot('form_method', 'POST')
		@slot('method', 'PATCH')
		@slot('form_action', 'alat-test.group.update')
		@slot('update_id', $item->id)

		@slot('is_form_with_file', 'true')

		@slot('input_form')
			@component('components.input-field')
				@slot('input_label', 'Nama Group')
				@slot('input_type', 'text')
				@slot('input_name', 'name')
				@slot('input_value') {{ $item->name }} @endslot
			@endcomponent

			@component('components.input-field')
				@slot('input_label', 'Deskripsi')
				@slot('input_type', 'text')
				@slot('input_name', 'description')
				@slot('input_value') {{ $item->description }} @endslot
			@endcomponent

			@component('components.input-field')
				@slot('input_label', 'Foto')
				@slot('input_type', 'file')
				@slot('input_name', 'photo')
			@endcomponent

			<div class="gallery gallery-fw">
				<a href="{{ asset('storage/'. $item->photo) }}" data-toggle="lightbox">
				<img src="{{ asset('storage/'. $item->photo) }}" class="img-fluid" style="min-width: 80px; height: auto;">
				</a>`
			</div>
		@endslot

		@slot('card_footer', 'true')
		@slot('card_footer_class', 'text-right')
		@slot('card_footer_content')
			@include('includes.save-cancel-btn')
		@endslot
	@endcomponent
@endsection
