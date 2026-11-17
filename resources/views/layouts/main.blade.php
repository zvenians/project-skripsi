<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#212529">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('photo/logounai.png') }}">
    <title>@yield('title', 'Fasilitas UNAI')</title>
    @include('partials.frontend-assets')
    @stack('styles')
</head>
<body>
    <a class="visually-hidden-focusable" href="#main-content">Lewati navigasi</a>
    @include('partials.navbar')
    <main id="main-content" class="container pb-5">
        @yield('container')
    </main>
    @include('partials.frontend-scripts')
    @stack('scripts')
</body>
</html>
