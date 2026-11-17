@extends('layouts.main')
@section('title', 'Tempat | Fasilitas UNAI')
@section('container')
    <section class="py-4 py-md-5">
        <div data-motion>
            <h1 class="h2 unai-page-heading">Beberapa Fasilitas Tempat di UNAI</h1>
            <p class="text-secondary mb-4">Temukan fasilitas yang tersedia dan lihat informasi masing-masing tempat.</p>
        </div>
        <div id="facility-tools" class="unai-surface p-3 p-md-4 mb-4" data-motion
             data-categories='@json($facility->pluck('category')->filter()->unique()->values())' v-cloak>
            <div class="unai-filter">
                <div><label class="form-label fw-semibold" for="facility-search">Cari fasilitas</label>
                    <input id="facility-search" v-model="query" type="search" class="form-control" placeholder="Nama atau deskripsi fasilitas" autocomplete="off"></div>
                <div><label class="form-label fw-semibold" for="facility-category">Kategori</label>
                    <select id="facility-category" v-model="category" class="form-select"><option value="all">Semua kategori</option><option v-for="item in categories" :key="item" :value="item" v-text="item"></option></select></div>
            </div>
            <p class="unai-filter-status mb-0" role="status" aria-live="polite">@{{ visible }} fasilitas ditampilkan</p>
        </div>
        <div class="unai-facility-grid" id="facility-grid">
            @foreach($facility as $f)
                <article class="card unai-facility-card unai-surface" data-facility-card data-motion data-motion-delay="{{ ($loop->index % 3) * .06 }}"
                    data-search="{{ $f->title }} {{ $f->category }} {{ $f->desc }}" data-category="{{ $f->category }}">
                    <img src="{{ asset('storage/' . $f->image) }}" alt="Foto {{ $f->title }}" loading="lazy" decoding="async">
                    <div class="card-body">
                        <span class="badge text-bg-light mb-2">{{ $f->category }}</span>
                        <h2 class="card-title">{{ $f->title }}</h2>
                        <p class="card-text text-secondary mb-0">{{ $f->desc }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        <div id="facility-empty" class="unai-empty mt-3" hidden role="status">Tidak ditemukan fasilitas sesuai pencarian.</div>
        @if($facility->isEmpty())<div class="alert alert-secondary">Belum ada fasilitas yang ditampilkan.</div>@endif
    </section>
@endsection
