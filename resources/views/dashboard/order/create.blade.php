@extends('dashboard.layouts.main')
@section('container')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Tambah Pesanan</h2>
            @if (session('sucess'))
            {{-- @dd(session()->get('status')) --}}
                <div class="alert alert-danger">{{ session('sucess') }}</div>
            @endif
            <form action="{{ url('order/store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    {{-- <select class="form-control" name="user_id">
                        <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                    </select> --}}
                    <input type="text" value="{{ Auth::user()->name }}" class="form-control"  readonly>
                    @error('user_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <br>
                <div class="form-group">
                    <label for="detil">Tujuan:</label>
                    <input type="text" class="form-control" name="detil" id="detil" value="{{ old('detil') }}"
                        placeholder="Masukkan Tujuan"></input>
                    @error('detil')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam">Waktu Mulai:</label>
                            <input type="datetime-local" class="form-control" name="jam" id="jam" value="{{ old('jam') }}"
                                placeholder="Masukkan Waktu"></input>
                            @error('jam')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end">Waktu Akhir:</label>
                            <input type="datetime-local" class="form-control" name="end" id="end" value="{{ old('end') }}"
                                placeholder="Masukkan Waktu"></input>
                            @error('end')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id">
                        {{-- <option value="">Pilih Fasilitas</option> --}}
                        @foreach ($facility as $f)
                            <option value="{{ $f->id }}"> {{ $f->title }}</option>
                        @endforeach
                    </select>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-warning">Reset</button>
                <a href="{{ url('dashboard/order') }}" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            flatpickr("#jam", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
            flatpickr("#end", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
        });
    </script>
@endsection
