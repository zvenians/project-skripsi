@extends('layouts.main')
@section('title', 'Jadwal Pemakaian | Fasilitas UNAI')
@section('container')
<section class="py-4 py-md-5" data-motion>
    <h1 class="h2 unai-page-heading mb-3">Berikut jadwal pemakaian tempat fasilitas UNAI</h1>
    <div class="card unai-surface mb-3"><div class="card-body">
        <form action="{{ url('search') }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-9"><label for="organisasi" class="form-label">Organisasi</label><select id="organisasi" name="organisasi" class="form-select">
                <option value="all">Semua organisasi</option>
                @foreach($user as $person)<option value="{{ $person->id }}" {{ (string)$id_user === (string)$person->id ? 'selected' : '' }}>{{ $person->name }}</option>@endforeach
            </select></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search" aria-hidden="true"></i> Cari</button></div>
        </form>
    </div></div>
    @if(empty($calendarEvents))<div class="alert alert-secondary">Belum ada pemakaian yang disetujui untuk filter ini.</div>@endif
    @include('partials.calendar')
</section>
@endsection
