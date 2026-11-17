@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Edit tempat</h2>
            <form action="{{ route('facility.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="img_lama" value="{{ $facility->image }}">
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="title">Tempat:</label>
                    <input type="title" class="form-control" name="title" id="title"
                        value="{{ $facility->title }}" ></input>
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select class="form-control" name="category" required>
                        <option value="Tersedia" {{ $facility->category == 'Tersedia' ? 'selected' : '' }}
                            class="success">Tersedia</option>
                        <option value="Dipakai" {{ $facility->category == 'Dipakai' ? 'selected' : '' }}
                            class="danger">Dipakai</option>
                    </select>
                </div>
                <div class="mb-3 mb-3">
                    <label for="image" class="form-label">Gambar tempat</label>
                    <input class="form-control" type="file" id="image" name="img">
                    @error('img')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                {{-- <div class="form-control">
                    <label for="desc">Deskripsi:</label>
                    <textarea calss="form-control" rows="4" name="desc" id="desc">{{ $facility->desc }}</textarea><br>
                </div> --}}
                <div class="form-group mb-3">
                    <label for="desc">Deskripsi:</label>
                    <textarea class="form-control" name="desc" id="desc"  rows="3">{{ $facility->desc }}</textarea>
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
