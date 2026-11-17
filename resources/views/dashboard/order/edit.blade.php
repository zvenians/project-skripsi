@extends('dashboard.layouts.main')
@section('container')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="row" data-motion>
        <div class="col-lg mt-3">
            <h2>Edit Pesanan</h2>
            <form action="{{ url('order/update' . '/' . $order) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" readonly required value="{{ $pengguna?->name ?? $data->user_id }}">
                </div>
                <br>
                <div class="form-group">
                    <label for="detil">Tujuan acara</label>
                    <input type="text" class="form-control" name="detil" email="detil" id="detil"
                        value="{{ $data->detil }}">
                        @error('detil')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                <br>
                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam">Waktu Mulai:</label>
                            <input type="text" class="form-control" name="jam" id="jam"
                                placeholder="Masukkan Waktu" value="{{ $data->date_end }}">
                            @error('jam')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end">Waktu Akhir:</label>
                            <input type="text" class="form-control" name="end" id="end"
                                placeholder="Masukkan Waktu" value="{{ $data->selesai }}">
                            @error('end')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select class="form-control" name="facility_id" required>
                        <option value="">Pilih Fasilitas</option>
                        @foreach ($facility as $f)
                            <option value="{{ $f->id }}" @if ($f->id == $data->facility_id)
                                selected
                            @endif>
                                {{ $f->title }} </option>
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
