@extends('layouts.main')

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <main class="form-registration">
                <form action="/register" method="POST">
                    @csrf
                    {{-- <img class="mb-4" src="/photo/logounai.png" alt="" width="100px"> --}}
                    <h1 class="h3 mb-3 fw-normal text-center mt-5">Registration Form</h1>

                    <div class="form-floating">
                        <input type="text" name="name" class="form-control @error('name')is-invalid @enderror" id="name"
                            placeholder="Name" value="{{ old('name') }}">
                        <label for="name">Name</label>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>
                    <div class="form-floating">
                        <input type="email" name="email" class="form-control @error('email')is-invalid @enderror" id="email"
                            placeholder="Email" value="{{ old('email') }}">
                        <label for="email">Email</label>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating">
                        <input type="password" name="password" class="form-control @error('password')is-invalid @enderror"
                            id="password" placeholder="password">
                        <label for="password">Password</label>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating">
                        <input type="tel" name="hp" class="form-control"
                            id="hp" placeholder="No Telephone" required>
                        <label for="hp">No Telephone</label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary mt-4" type="submit">Register</button>
                </form>
                <small class="">Already Registered?<a href="/login">Login</a></small>
            </main>
        </div>
    </div>

@endsection
