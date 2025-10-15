
@extends('layouts.main')

@section('title')
    Detail Data Ruangan - ROOMING
@endsection

{{-- #original: array:12 [▼
"id" => 2
"username" => "10050021234"
"npm" => "10050021234"
"email" => "mhs1@univ.ac.id"
"password" => "$2y$10$nYj156L0xlmNL9omDbZwPOwewtlDVG10OKeYrEulIp3z5RG1SRi4K"
"name" => "Mahasiswa 1"
"description" => null
"role" => "USER"
"deleted_at" => null
"remember_token" => null
"created_at" => "2025-07-23 16:57:09"
"updated_at" => "2025-08-16 18:29:41"
] --}}

@section('content')
    {{-- <div class="container mt-5">
        <div class="card">
            <div class="profile-header text-center">
                <h2>&nbsp;</h2>
            </div>
            <div class="card-body text-center">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp" alt="Avatar" style="width: 177px;" class="profile-picture shadow">
                <h3 class="mt-3">{{ $user->name }}</h3>
                <p class="text-muted">Email: {{ $user->email }}</p>
                <p class="text-muted">Lokasi: Bandung, Indonesia</p>

                <hr>

                <div class="row text-start mt-4">
                    <div class="col-md-6">
                        <h5>Informasi Pribadi</h5>
                        <ul class="list-unstyled">
                            <li><strong>NPM:</strong> {{ $user->npm }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Bio</h5>
                        <p>Saya adalah seorang pengembang web yang menyukai desain bersih dan fungsional. Saya memiliki pengalaman dalam HTML, CSS, JavaScript, dan framework modern seperti React dan Laravel.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="#" class="btn btn-primary">Edit Profil</a>
                    <a href="#" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="container py-5">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card card-profile shadow">
              <div class="bg-profile position-relative">
              </div>
              <div class="card-body text-center mt-n5">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp" style="width: 257px;" class="profile-img shadow" alt="Foto Profil">
                <h4 class="mt-2">{{ $user->name }}</h4>
                <ul class="list-unstyled small">
                    <li><strong>NPM:</strong> {{ $user->npm }}</li>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                </ul>

                <div class="">
                    <h5>Bio</h5>
                    <p>{{ $user->description }}</p>
                </div>
      
                <hr>
      
                <div class="text-center">
                  <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Edit Profil</a>
                  <a href="javascript:void(0)" class="btn btn-secondary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection