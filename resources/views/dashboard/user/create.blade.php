@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Tambah Pengguna</h2>
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="name">Nama:</label>
                    <input type="text" value="{{ old('name') }}"  class="form-control" name="name" id="name" placeholder="Masukkan nama"></input>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email:</label>
                    <input type="email" value="{{ old('email') }}" class="form-control" name="email" id="email" placeholder="Masukkan email"></input>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password:</label>
                    <input type="password"  class="form-control" name="password" id="password"
                        placeholder="Masukkan password"></input>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="hp">No Hp:</label>
                    <input type="tel"  class="form-control" name="hp" id="hp"
                        placeholder="Masukkan No Hp"></input>
                        @error('hp')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-warning">Reset</button>
                <a href="{{ url('dashboard/user') }}" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
@endsection
