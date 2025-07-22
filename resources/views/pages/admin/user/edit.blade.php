@extends('layouts.main')

@section('title')
    Edit Data User - ROOMING
@endsection

@section('header-title')
    Edit Data User
@endsection

@section('breadcrumbs')
    <div class="breadcrumb-item"><a href="#">User</a></div>
    <div class="breadcrumb-item"><a href="{{ route('user.index') }}">Data User</a></div>
    <div class="breadcrumb-item active">{{ $item->name }}</div>
@endsection

@section('section-title')
    Edit Data User
@endsection

@section('section-lead')
    Silakan isi form di bawah ini untuk mengedit data <strong>{{ $item->name }}</strong>.
@endsection

@section('content')

  @component('components.form')
    @slot('row_class', 'justify-content-center')
    @slot('col_class', 'col-12 col-md-6')

    @slot('form_method', 'POST')
    @slot('method', 'PUT')
    @slot('form_action', 'user.update')
    @slot('update_id', $item->id)

    @slot('input_form')

      {{-- Nama --}}
      @component('components.input-field')
          @slot('input_label', 'Nama')
          @slot('input_type', 'text')
          @slot('input_name', 'name')
          @slot('input_value', old('name', $item->name))
          @slot('form_group_class', 'required')
          @slot('other_attributes', 'required autofocus')
      @endcomponent
      @error('name')
        <div class="text-danger small">{{ $message }}</div>
      @enderror

      {{-- Email --}}
      @component('components.input-field')
          @slot('input_label', 'Email')
          @slot('input_type', 'text')
          @slot('input_name', 'email')
          @slot('input_value', old('email', $item->email))
          @slot('form_group_class', 'required')
          @slot('other_attributes', 'required')
      @endcomponent
      @error('email')
        <div class="text-danger small">{{ $message }}</div>
      @enderror

      {{-- Role --}}
      @component('components.select-field')
          @slot('input_label', 'Role')
          @slot('input_name', 'role')
          @slot('form_group_class', 'required')
          @slot('other_attributes', 'required')
          @php
              $options = [
                  'ADMIN' => 'Admin',
                  'USER' => 'User'
              ];
          @endphp
          @slot('options', $options)
          @slot('selected', old('role', $item->role ?? null))
      @endcomponent
      @error('role')
        <div class="text-danger small">{{ $message }}</div>
      @enderror

      {{-- Keterangan --}}
      @component('components.input-field')
          @slot('input_label', 'Keterangan')
          @slot('input_type', 'text')
          @slot('input_name', 'description')
          @slot('input_value', old('description', $item->description))
      @endcomponent
      @error('description')
        <div class="text-danger small">{{ $message }}</div>
      @enderror

    @endslot

    @slot('card_footer', 'true')
    @slot('card_footer_class', 'd-flex justify-content-between align-items-center')
    @slot('card_footer_content')
        @include('includes.save-cancel-btn')
    @endslot

  @endcomponent

@endsection
