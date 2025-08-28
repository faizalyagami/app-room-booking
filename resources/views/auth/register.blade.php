@extends('layouts.app')

@section('title', 'Register - ROOMING')

@section('content')
<section class="section">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2">

                <div class="card card-primary p-5">
                    <div class="card-body">
                        <div class="logo" align="center">
                            <img src="{{ asset('theme/img/logo-unisba.png') }}" width="120">
                        </div>
                        <div class="login-brand">
                            SISTEM INFORMASI MANAJEMEN BOOKING RUANGAN & PEMINJAMAN ALAT TEST LABORATORIUM PSIKOLOGI
                        </div>
                        
                        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
                            @csrf
    
                            <div class="form-group row">
                                <label for="name">{{ __('NPM') }}</label>
                                <input id="npm" type="text" class="form-control @error('npm') is-invalid @enderror" name="npm" value="{{ old('npm') }}" required autocomplete="npm" autofocus>

                                @error('npm')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
    
                            <div class="form-group row">
                                <label for="name">{{ __('Name') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
    
                            <div class="form-group row">
                                <label for="email">{{ __('E-Mail Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
    
                            <div class="form-group row">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
    
                            <div class="form-group row">
                                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
    
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                    &nbsp;&nbsp;&nbsp;
                                    <a href="{{ route('login') }}" class="btn btn-secondary">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
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
