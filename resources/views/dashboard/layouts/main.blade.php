<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#212529">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | Fasilitas UNAI')</title>
    <link rel="icon" href="{{ asset('photo/logounai.png') }}">
    @include('partials.frontend-assets')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body class="unai-dashboard">
    @include('dashboard.layouts.header')
    <div class="container-fluid">
        <div class="row">
            @include('dashboard.layouts.sidebar')
            <main id="main-content" class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pb-5">
                @yield('container')
            </main>
        </div>
    </div>
    @include('partials.frontend-scripts')
    @stack('scripts')
</body>
</html>
