@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Tambah Fasilitas</h2>
            <form action="{{ route('facility.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="title">Nama Tempat:</label>
                    <input type="text" class="form-control" name="title" id="title"
                        placeholder="Masukkan nama tempat" value="{{ old('title') }}">
                        @error('title')
                    <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select class="form-control" name="category" required>
                        {{-- <option value="">Plih Status</option> --}}
                        <option value="Tersedia" class="success">Tersedia</option>
                        <option value="Dipakai" class="danger">Dipakai</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Gambar tempat</label>
                    <input class="form-control" type="file"  id="image" name="img">
                    @error('img')
                    <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="desc">Deskripsi:</label>
                    <textarea class="form-control" name="desc" id="desc" rows="3" >{{ old('desc') }}</textarea>
                    @error('desc')
                    <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-warning">Reset</button>
                <a href="{{ url('dashboard/facility') }}" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>

@endsection
