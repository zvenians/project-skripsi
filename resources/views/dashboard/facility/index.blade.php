@extends('dashboard.layouts.main')
@section('container')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Halo, {{ Auth::user()->name }}</h1>
    </div>
    <h4>Fasilitas</h4>
    <div class="table-responsive mb-5">
        <p><a class="btn btn-primary" href="{{ route('facility.create') }}"><i class="fas fa-plus"></i> Tambah Fasilitas</a></p>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-striped table-sm" id="myTable">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Tempat</th>
                    <th scope="col">Category</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">deskripsi</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                    {{-- <th scope="col">Header</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($facility as $f)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $f->title }}</td>
                        <td>{{ $f->category }}</td>
                        <td>
                            <div style="width: 100px;height: 100px">
                                <img style="height: 100%;width: 100%;object-fit: cover" src="{{ asset('storage/' . $f->image) }}"
                                    alt="{{ $f->image }}">
                            </div>
                        </td>
                        <td>{{ $f->desc }} </td>
                        <td><a class="btn btn-warning" href="{{ route('facility.edit', $f->id) }}"><i class="fas fa-pen"></i></a></td>
                        <td>
                            <form onsubmit="return confirm('Apakah anda yakin?');"
                                action="{{ route('facility.destroy', $f->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger " type="submit"><i class="fas fa-trash"></i></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready( function () {
    $('#myTable').DataTable();
} );
    </script>
@endsection
