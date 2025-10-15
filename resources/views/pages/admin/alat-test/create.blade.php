@extends('layouts.main')

@section('title')
	Tambah Data Alat Test - ROOMING
@endsection

@section('header-title')
	Tambah Data Alat Test
@endsection

@section('breadcrumbs')
	<div class="breadcrumb-item"><a href="#">Data Master</a></div>
	<div class="breadcrumb-item">Alat Test</div>
	<div class="breadcrumb-item active">Tambah Data</div>
@endsection

@section('section-title')
	Tambah Data Alat Test
@endsection

@section('section-lead')
  	Silakan isi form di bawah ini untuk menambah data Alat Test.
@endsection

@section('content')
	@component('components.form')
		@slot('row_class', '')
		@slot('col_class', 'col-12')

		@slot('form_method', 'POST')
		@slot('form_action', 'alat-test.store')

		@slot('input_form')
			@component('components.select-field', [
				'input_label' => 'Group',
				'input_name' => 'alat_test_id',
				'options' => $groups
			])
			@endcomponent

			@component('components.input-field')
				@slot('input_label', 'Serial Number')
				@slot('input_type', 'text')
				@slot('input_name', 'serial_number')
				@slot('input_value') {{ old('serial_number') }} @endslot
			@endcomponent

			<div class="form-group">
				<label class="input-label">Serial Number</label>

				<div class="col-sm-10">
					@foreach($types as $key => $value)
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="type" id="type-{{ $key }}" value="{{ $key }}" {{ $key == (old('type') ?? 1) ? 'checked' : '' }}>
							<label class="form-check-label" for="type-{{ $key }}">
								{{ $value }}
							</label>
						</div>
					@endforeach
				</div>
			</div>

			<div class="form-group" id="group-amount">
				<label class="input-label">Jumlah</label>
				<input type="number" name="quantity" class="form-control" value="{{ old('amount') }}" />
		  	</div>
		@endslot

		@slot('card_footer', 'true')
		@slot('card_footer_class', 'text-right')
		@slot('card_footer_content')
			@include('includes.save-cancel-btn')
		@endslot
	@endcomponent
@endsection


@push('after-script')
	<script>
		$(document).ready(function () {
			$("#group-amount").hide()

			if($("input[name=type]:checked").val() == 2) {
				$("#group-amount").show()
			}

			$("input[name=type]").on('change', function() {
				$("input[name=amount]").val('')
				$("#group-amount").hide()

				if (this.value == 2) {
					$("#group-amount").show()
				}
			})
		});
	</script>
@endpush