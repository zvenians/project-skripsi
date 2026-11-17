<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" aria-label="Header dashboard">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ url('/dashboard/utama') }}"><img src="{{ asset('photo/logounai.png') }}" width="36" height="36" alt="Logo UNAI"> Fasilitas UNAI</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Buka navigasi dashboard"><span class="navbar-toggler-icon"></span></button>
    <form role="search" class="flex-grow-1 d-none d-md-block" onsubmit="return false">
        <label class="visually-hidden" for="dashboardQuickFilter">Cari pada tabel di halaman ini</label>
        <input id="dashboardQuickFilter" type="search" class="form-control form-control-dark w-100" placeholder="Cari pada tabel di halaman ini…" aria-label="Cari data">
    </form>
    <div class="navbar-nav"><div class="nav-item text-nowrap">
        @if(config('demo.enabled'))
            <a class="nav-link px-3 text-white" href="{{ url('/') }}">Halaman Utama <i class="bi bi-house" aria-hidden="true"></i></a>
        @else
            <form action="{{ url('/logout') }}" method="post">@csrf<button type="submit" class="nav-link px-3 bg-dark border-0 text-white">Keluar <i class="bi bi-box-arrow-right" aria-hidden="true"></i></button></form>
        @endif
    </div></div>
</header>
