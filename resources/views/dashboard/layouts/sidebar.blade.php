<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" style="color: whitesmoke">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard/utama') ? 'active' : '' }}" aria-current="page" href="{{ url('/dashboard/utama') }}"
                    style="color: whitesmoke">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard/order*') ? 'active' : '' }}" href="/dashboard/order"
                    style="color: whitesmoke">
                    <span data-feather="align-justify"></span>
                    Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard/schedule*') ? 'active' : '' }}" href="/dashboard/schedule"
                    style="color: whitesmoke">
                    <span data-feather="file-minus"></span>
                    Jadwal Organisasi
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard/facility*') ? 'active' : '' }}" href="/dashboard/news"
                    style="color: whitesmoke">
                    <span data-feather="bar-chart-2"></span>
                    Kegiatan
                </a>
            </li> --}}
        </ul>


        @can('admin')
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Administrator</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/user*') ? 'active' : '' }}" href="/dashboard/facility"
                        style="color: whitesmoke">
                        <span data-feather="server"></span>
                        Fasilitas 
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard/user"
                        style="color: whitesmoke">
                        <span data-feather="user"></span>
                        Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="/dashboard/suggestion"
                        style="color: whitesmoke">
                        <span data-feather="layers"></span>
                        Kritik dan Saran
                    </a>
                </li>
            </ul>
        @endcan
    </div>
</nav>
