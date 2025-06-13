@extends('layouts.app')

@section('title', 'Login - ROOMING')

@section('content')
<section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

          <div class="card card-primary">
            <div class="card-body">
              <div class="logo" align="center">
                <img src="{{ asset('theme/img/logo-unisba.png') }}" width="120">
              </div>
              <div class="login-brand">
                SISTEM INFORMASI MANAJEMEN BOOKING RUANGAN & PEMINJAMAN ALAT TEST LABORATORIUM PSIKOLOGI
              </div>
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group">
                  <label for="username">Username</label>
                  <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                    {{ __('Login') }}
                  </button>
                </div>
              </form>
              
            </div>
          </div>
          
          <div class="simple-footer">
            Copyright &copy; Laboratorium Psikologi UNISBA. <br>
            Powered by &copy; Fakultas Psikologi UNISBA 2025
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
