@extends('layouts.main')

@section('container')
    <!-- Masthead-->
    <div class="container masthead" data-motion style="padding-top: 2em; ">
        <div class="container px-4 px-lg-5 h-100">
            <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end">
                    <h1 class="text-muted font-weight-bold">Selamat datang di website pemesanan fasilitas Universitas Advent
                        Indonesia</h1>
                    <hr class="divider" />
                </div>
                <div class="col-lg-8 align-self-baseline">
                    <p class="text-muted-75 mb-5">Website ini menyediakan layanan pemesanan terhadap fasilitas yang terdapat
                        di dalam Universitas Advent Indonesia yang akan mempermudah para organisasi dalam memesan sebuah
                        fasilitas untuk dipakai.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel -->
    <div class="container-fluid">
        <div id="demo" class="carousel slide unai-carousel" data-motion data-bs-ride="carousel" style="padding-top: 2em">

            <!-- Indicators/dots -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
            </div>

            <!-- The slideshow/carousel -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="/photo/101.jpeg" alt="Ruang NACB 101" class="d-block" style="width:100%">
                    <div class="carousel-caption">
                        <h3>NACB 101</h3>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="/photo/102.jpeg" alt="Ruang NACB 102" class="d-block" style="width:100%">
                    <div class="carousel-caption">
                        <h3>NACB 102</h3>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="/photo/NDG.jpeg" alt="Gedung NDG" class="d-block" style="width:100%">
                    <div class="carousel-caption">
                        <h3>NDG</h3>
                    </div>
                </div>
            </div>

            <!-- Left and right controls/icons -->
            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
@endsection
