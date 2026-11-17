@extends('dashboard.layouts.main')
@section('container')
    <div class="row" data-motion>
        <div class="col-md-6">
            <h2>Edit tempat</h2>
            <form action="{{ route('schedule.update', $schedule->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama</label>
                    <select class="form-control" name="user_id">
                        {{-- <option value="">Pilih User</option> --}}
                        @foreach ($user as $u)
                            <option value="{{ $u->id }}" {{ $u->id == $schedule->user_id ? 'selected' : '' }}>
                                {{ $u->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="day">Hari:</label>
                    <select name="day" id="day" class="form-control" required>
                        <option value="Senin" @if ($schedule->day == "Senin")
                            selected
                        @endif>Senin</option>
                        <option value="Selasa"  @if ($schedule->day == "Selesa")
                            selected
                        @endif>Selasa</option>
                        <option value="Rabu"  @if ($schedule->day == "Rabu")
                            selected
                        @endif>Rabu</option>
                        <option value="Kamis"  @if ($schedule->day == "Kamis")
                            selected
                        @endif>Kamis</option>
                        <option value="Jum'at"  @if ($schedule->day == "Jum'at")
                            selected
                        @endif>Jum'at</option>
                        <option value="Sabtu"  @if ($schedule->day == "Sabtu")
                            selected
                        @endif>Sabtu</option>
                        <option value="Minggu"  @if ($schedule->day == "Minggu")
                            selected
                        @endif>Minggu</option>
                    </select>
                </div>
                {{-- <div class="form-group">
                    <label for="time_s">Jam:</label>
                    <input type="text" class="form-control" name="time_s" id="time_s" placeholder="Masukkan Tanggal"
                        value="{{ $schedule->time_s }}"></input>
                </div> --}}
                <div class="form-group">
                    <label for="time_s">Jam Mulai:</label>
                    <input type="time" value="{{ $schedule->time_s }}"  class="form-control" name="time_s" id="time_s" placeholder="Masukkan jam">
                    @error('time_s')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="selesai">Jam Selesai:</label>
                    <input type="time" value="{{ $schedule->time_end }}"  class="form-control" name="selesai" id="selesai" placeholder="Masukkan jam"></input>
                    @error('selesai')
                    <small class="texxt-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id" required>
                        {{-- <option value="">Pilih Fasilitas</option> --}}
                        @foreach ($facility as $f)
                            <option value="{{ $f->id }}" {{ $f->id == $schedule->facility_id ? 'selected' : '' }}>
                                {{ $f->title }} </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-warning">Reset</button>
            </form>
        </div>
    </div>
@endsection
