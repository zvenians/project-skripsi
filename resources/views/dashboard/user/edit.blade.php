@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Edit Pengguna</h2>
            <form action="{{ route('user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name">Nama:</label>
                    <input type="text"  class="form-control" name="name" id="name" value="{{ $user->name }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email:</label>
                    <input type="email"  class="form-control" name="email" email="email" id="email"
                        value="{{ $user->email }}">
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="no_hp">No Hp:</label>
                    <input type="number"  class="form-control" name="no_hp" id="no_hp"
                        value="{{ $user->no_hp }}">
                        @error('no_hp')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- <div class="form-group">
                    <label for="password">password:</label>
                    <input type="password"  class="form-control" name="password" id="password"
                        value="{{ $user->password }}"></input><br>
                </div> --}}
                {{-- <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="category">
                        <option value="">Pilih status</option>
                        <option value="Tersedia" {{ $user->category == 'Tersedia' ? 'selected' : '' }}
                            class="success">Tersedia</option>
                        <option value="Dipakai" {{ $user->category == 'Dipakai' ? 'selected' : '' }}
                            class="danger">Dipakai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="desc">Deskripsi:</label>
                    <textarea calss="form-control" rows="4" name="desc" id="desc">{{ $user->desc }}</textarea><br>
                </div> --}}
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-warning">Reset</button>
                <a href="{{ url('dashboard/user') }}" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
@endsection
