@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-md-6">
            <h2>Tambah schedule</h2>
            <form action="{{ route('schedule.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <select class="form-control" name="user_id" required>
                        <option value="">Pilih Organisasi</option>
                        @foreach ($user as $u)
                            <option value="{{ $u->id }}"> {{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="day">Hari:</label>
                    {{-- <input type="text" class="form-control" name="day" id="day" placeholder="Masukkan Hari"></input> --}}
                    <select name="day" id="day" class="form-control" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jum'at">Jum'at</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="time_s">Jam Mulai:</label>
                    <input type="time" value="{{ old('time_s') }}" class="form-control" name="time_s" id="time_s"
                        placeholder="Masukkan jam"></input>
                    @error('time_s')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="selesai">Jam Selesai:</label>
                    <input type="time" value="{{ old('selesai') }}" class="form-control" name="selesai" id="selesai"
                        placeholder="Masukkan jam">
                    @error('selesai')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id" required>
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
@endsection
