@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Tambah Kritik/Saran</h2>
            <form action="{{ route('suggestion.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="name_s">Nama:</label>
                    <input type="text" class="form-control" name="name_s" id="name_s"  placeholder="Masukkan nama">
                    @error('name_s')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select class="form-control" name="category_s" required>
                        {{-- <option value="">Plih Status</option> --}}
                        <option value="Kritik" class="success">Kritik</option>
                        <option value="Saran" class="danger">Saran</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="critic">Kritik & Saran:</label>
                    <textarea  class="form-control" name="critic" id="critic" rows="3"></textarea>
                    @error('critic')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-warning">Reset</button>
                <a href="{{ url('dashboard/suggestion') }}" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
@endsection
