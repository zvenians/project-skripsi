<nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('photo/logounai.png') }}" width="40" height="40" alt="Logo Universitas Advent Indonesia"> <span>Fasilitas UNAI</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->is('/') || request()->is('home') ? 'active' : '' }}" href="{{ url('/') }}" {{ request()->is('/') ? 'aria-current=page' : '' }}>Halaman Utama</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('tempat') ? 'active' : '' }}" href="{{ url('/tempat') }}" {{ request()->is('tempat') ? 'aria-current=page' : '' }}>Tempat</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('jadwal') ? 'active' : '' }}" href="{{ url('/jadwal') }}" {{ request()->is('jadwal') ? 'aria-current=page' : '' }}>Jadwal Pemakaian</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('kritik') ? 'active' : '' }}" href="{{ url('/kritik') }}" {{ request()->is('kritik') ? 'aria-current=page' : '' }}>Kritik &amp; Saran</a></li>
            </ul>
            <form action="{{ url('/tempat') }}" method="GET" class="d-flex my-2 my-lg-0 me-lg-3" role="search">
                <label class="visually-hidden" for="navFacilitySearch">Cari fasilitas</label>
                <input id="navFacilitySearch" class="form-control me-2" name="q" type="search" placeholder="Cari fasilitas" value="{{ request('q') }}">
                <button class="btn btn-outline-light" type="submit">Cari</button>
            </form>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ config('demo.enabled') ? 'Pengunjung Demo' : auth()->user()->name }}</a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ url('/dashboard/utama') }}"><i class="bi bi-grid-1x2 me-2" aria-hidden="true"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(config('demo.enabled'))
                                <li><a class="dropdown-item" href="{{ url('/') }}"><i class="bi bi-house me-2" aria-hidden="true"></i>Halaman Utama</a></li>
                            @else
                                <li><form action="{{ url('/logout') }}" method="post">@csrf <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar</button></form></li>
                            @endif
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->is('login') ? 'active' : '' }}" href="{{ url('/login') }}"><i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>{{ config('demo.enabled') ? 'Masuk Demo' : 'Login' }}</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
