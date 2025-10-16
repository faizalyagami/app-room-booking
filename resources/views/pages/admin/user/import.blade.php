@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Import Mahasiswa</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.importExcel') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File Excel</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Import</button>
        <a href="{{ route('user.downloadTemplate') }}" class="btn btn-success mt-2">Download Template</a>
    </form>
</div>
@endsection
