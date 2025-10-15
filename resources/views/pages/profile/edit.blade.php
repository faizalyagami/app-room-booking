@extends('layouts.main')

@section('title')
  Profile - ROOMING
@endsection 

@section('header-title')
  Edit Profile
@endsection 
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="javascript:void(0)">Profile</a></div>
@endsection

@section('section-title')
  Edit Profile
@endsection 
    
@section('section-lead')
  Silakan isi form di bawah ini untuk mengupdate data diri anda.
@endsection

@section('content')

<div class="row">
  <div class="col ">
    <div class="card">
      <div class="card-body">

        <form action="{{ route('profile.update') }}" method="post" name="form-profile" id="form-profile">
          @method('patch')
          @csrf

          <div class="form-group">
            <label for="date">Email</label>
            <input type="text" name="email" class="form-control" value="{{ $user->email }}">
          </div>
          <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" class="form-control" id="description" style="height: 185px;">{{ $user->description }}</textarea>
          </div>

          <div class="card-footer  text-right ">
            <button class="btn btn-primary">Simpan</button>
            <a type="button" href="{{ route('profile') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection

@section('modal-box')
 
@endsection

@push('after-style')
  
@endpush

@push('after-script')
  <script>
    
  </script>
@endpush

@include('includes.notification')