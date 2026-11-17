@extends('layouts.main')

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-5">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('loginError'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('loginError') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <main class="form-signin">
                <form action="/login" method="post">
                    @csrf
                    {{-- <img class="mb-4" src="/photo/logounai.png" alt="" width="100px"> --}}
                    <h1 class="h3 mb-3 mt-5 fw-normal text-center">Please log in</h1>
                    @if(config('demo.enabled'))
                        <p class="text-center text-muted small mb-3" role="status">Mode demo — masuk tanpa email atau password.</p>
                    @endif

                    <div class="form-floating">
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid

                        @enderror" id="email"
                            placeholder="name@example.com" @if(!config('demo.enabled')) autofocus required @else disabled @endif value="{{ old('email') }}">
                        <label for="email">Email address</label>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating">
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password"
                            @if(!config('demo.enabled')) required @else disabled @endif>
                        <label for="password">Password</label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary" type="submit">{{ config('demo.enabled') ? 'Masuk ke Demo' : 'Log in' }}</button>
                </form>
                @if(config('demo.enabled'))
                    <small class="d-block mt-2 text-center text-muted">Semua fitur demo dapat dijelajahi tanpa membuat akun.</small>
                @else
                    <small class="">Not Registered?<a href="/register">Register Now!</a></small>
                @endif
            </main>
        </div>
    </div>

@endsection
