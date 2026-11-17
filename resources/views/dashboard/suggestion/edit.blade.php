@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Edit Kritik Dan Saran</h2>
            <form action="{{ route('suggestion.update', $suggestion->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name_s">Tempat:</label>
                    <input type="name_s" class="form-control" name="name_s" id="name_s"
                        value="{{ $suggestion->name_s }}">
                        @error('name_s')
                            <small class="text-danger">{{  $message }}</small>
                        @enderror
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select class="form-control" name="category_s" required>
                        {{-- <option value="">Pilih status</option> --}}
                        <option value="Kritik" {{ $suggestion->category_s == 'Kritik' ? 'selected' : '' }}
                            class="success">Kritik</option>
                        <option value="Saran" {{ $suggestion->category_s == 'Saran' ? 'selected' : '' }}
                            class="danger">Saran</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Kritik & Saran</label>
                    <textarea  class="form-control" name="kritik" id="desc" rows="3">{{ $suggestion->critic }}</textarea>
                    @error('kritik')
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
