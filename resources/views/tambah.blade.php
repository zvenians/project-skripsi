@extends('layouts.main')

@section('container')
<div class="row">
    <div class="col-lg mt-5">
        <h2>Tambah Kritik/Saran</h2>
        <form action="{{ url('suggestion/store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name_s">Nama:</label>
                <input type="text"  class="form-control" name="name_s" id="name_s" placeholder="Masukkan nama" value="{{ old('names_s') }}">
                @error('name_s')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <br>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="category_s" required>
                    <option value="Kritik" class="success">Kritik</option>
                    <option value="Saran" class="danger">Saran</option>
                </select>
            </div>
            <br>
            <div class="form-group">
                <label for="critic">Kritik & Saran:</label>
                <textarea class="form-control"  name="critic" id="critic" rows="3" >{{ old('critic') }}</textarea>
                @error('critic')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="reset" class="btn btn-warning">Reset</button>
        </form>
    </div>
</div>
@endsection
