@extends('dashboard.layouts.main')
@section('container')

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 bnews-bottom">
        <h1 class="h2">Halo, {{ auth()->user()->name }}</h1>
    </div>
    <div class="table-responsive" data-motion>
        <p><a class="btn btn-primary" href="{{ route('news.create') }}">Tambah Kegiatan</a></p>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Event</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jenis Kegiatan</th>
                    <th scope="col">Tempat</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                    {{-- <th scope="col">Header</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($news as $n)
                        @php
                            $maint  = DB::table('facilities')->where('id',$n->facility_id)->first();
                            $user = DB::table('users')->where('id',$n->user_id)->first();
                        @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $n->desc_n }}</td>
                        <td>{{ date("d,F Y H:i:s",strtotime($n->time)) }}</td>
                        <td>{{ $n->category_n }}</td>
                        {{-- <td>{{ $n->facility->title }}</td> --}}
                        <td>{{ $maint->title }}</td>
                        <td><a class="btn btn-warning" href="{{ route('news.edit', $n->id) }}">Edit</a></td>
                        <td>
                            <form onsubmit="return confirm('Apakah anda yakin?');"
                                action="{{ route('news.destroy', $n->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
