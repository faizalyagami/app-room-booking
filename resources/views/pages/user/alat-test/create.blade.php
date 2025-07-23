@extends('layouts.main')

@section('title', 'Pinjam Alat Test - ROOMING')
@section('header-title', 'Peminjaman Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item"><a href="{{ route('alat-test-booking.index') }}">Peminjaman Alat Test</a></div>
  <div class="breadcrumb-item active">Pinjam Alat</div>
@endsection

@section('section-title', 'Peminjaman Alat Test')
@section('section-lead', 'Silakan isi form berikut untuk melakukan peminjaman alat test.')

@section('content')
  @component('components.form')
    @slot('row_class', 'justify-content-center')
    @slot('col_class', 'col-12 col-md-6')

    @slot('form_method', 'POST')
    @slot('form_action', 'alat-test-booking.store')

    @slot('input_form')

      @component('components.input-field')
        @slot('input_label', 'Pilih Alat')
        @slot('input_type', 'select')
        @slot('select_content')
          <option value="">Pilih Alat</option>
          @foreach ($alatTests as $alat)
            <option value="{{ $alat->id }}">{{ $alat->name }}</option>
          @endforeach
        @endslot
        @slot('input_name', 'alat_test_id')
        @slot('form_group_class', 'required')
        @slot('other_attributes', 'required')
      @endcomponent

      @component('components.input-field')
        @slot('input_label', 'Tanggal Peminjaman')
        @slot('input_type', 'text')
        @slot('input_name', 'borrow_date')
        @slot('input_classes', 'datepicker')
        @slot('form_group_class', 'required')
        @slot('other_attributes', 'required')
      @endcomponent

      @component('components.input-field')
        @slot('input_label', 'Keperluan')
        @slot('input_type', 'text')
        @slot('input_name', 'purpose')
        @slot('form_group_class', 'required')
        @slot('other_attributes', 'required')
      @endcomponent

    @endslot

    @slot('card_footer', true)
    @slot('card_footer_class', 'text-right')
    @slot('card_footer_content')
      @include('includes.save-cancel-btn')
    @endslot

  @endcomponent
@endsection

@push('after-style')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('after-script')
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
