@extends('dashboard.layouts.main')
@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 bnews-bottom">
        <h1 class="h2">Selamat datang kembali, {{ auth()->user()->name }}</h1>
    </div>
        <div id="carouselExampleFade" class="carousel slide carousel-fade unai-carousel" data-motion data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="/photo/101.jpeg" class="d-block w-100" alt="ruangan 101">
              </div>
              <div class="carousel-item">
                <img src="/photo/102.jpeg" class="d-block w-100" alt="ruangan NACB 102">
              </div>
              <div class="carousel-item">
                <img src="/photo/NDG.jpeg" class="d-block w-100" alt="ruangan NDG">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
@endsection
