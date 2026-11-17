@extends('dashboard.layouts.main')
@section('container')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@if (session('status'))
    <div class="alert alert-danger">{{ session('status') }}</div>
@endif
    <div class="row" data-motion>
        <div class="col-md-6">
            <h2>Edit tempat</h2>
            <form action="{{ route('news.update', $news->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama</label>
                    <select class="form-control" name="user_id" required>
                        {{-- <option value="">Pilih User</option> --}}
                        @foreach ($user as $u)
                            <option value="{{ $u->id }}" {{ $u->id == $news->user_id ? 'selected' : '' }}>
                                {{ $u->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="desc_n">Event:</label>
                    <input type="text" class="form-control" name="desc_n" id="desc_n" placeholder="Masukkan Event"
                        value="{{ $news->desc_n }}">
                        @error('desc_n')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group">
                    <label for="time">Tanggal:</label>
                    <input  class="form-control" name="time" id="time" required placeholder="Masukkan Tanggal"
                        value="{{ date("Y-m-d H:i:s",strtotime($news->time)) }}">
                        @error('time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group">
                    <label for="category_n">Jenis Kegiatan:</label>
                    <input type="text" class="form-control" name="category_n" id="category_n" placeholder="Masukkan jenis"
                        value="{{ $news->category_n }}">
                        @error('category_n')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id" required>
                        {{-- <option value="">Pilih Fasilitas</option> --}}
                        @foreach ($facility as $f)
                            <option value="{{ $f->id }}" {{ $f->id == $news->facility_id ? 'selected' : '' }}>
                                {{ $f->title }} </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-warning">Reset</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function(){
        flatpickr("#time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            // allowInput: true,
        });
        });
    </script>
@endsection
