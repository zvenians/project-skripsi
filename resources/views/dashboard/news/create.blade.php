@extends('dashboard.layouts.main')
@section('container')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @if (session('status'))
        <div class="alert alert-danger">{{ session('status') }}</div>
    @endif
    <div class="row" data-motion>
        <div class="col-md-6">
            <h2>Tambah Kegiatan</h2>
            <form action="{{ route('news.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <select class="form-control" name="user_id">
                        {{-- <option value="">Pilih User</option> --}}
                        <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="desc_n">Event:</label>
                    <input type="text" class="form-control" name="desc_n" id="desc_n" placeholder="Masukkan Event"
                        value="{{ old('desc_n') }}"></input>
                    @error('desc_n')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="time">Tanggal:</label>
                    <input type="datetime-local" class="form-control" name="time" id="time" placeholder="Masukkan Tanggal"
                        value="{{ old('time') }}"></input>
                    @error('time')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category_n">Jenis Kegiatan:</label>
                    <input type="text" class="form-control" name="category_n" id="category_n" placeholder="Masukkan jenis"
                        value="{{ old('category_n') }}"></input>
                    @error('category_n')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id">
                        {{-- <option value="">Pilih Fasilitas</option> --}}
                        @foreach ($facility as $f)
                            <option value="{{ $f->id }}"> {{ $f->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-warning">Reset</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            flatpickr("#time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
        });
    </script>
@endsection
