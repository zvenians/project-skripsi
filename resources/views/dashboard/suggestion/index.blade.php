@extends('dashboard.layouts.main')
@section('container')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Halo,{{ Auth::user()->name }}</h1>
    </div>
    <h4>Kritik & Saran</h4>
    <div class="table-responsive mb-3">
        <p><a class="btn btn-primary" href="{{ url('suggestion/create') }}"><i class="fas fa-plus"></i> Tambah Kritik & Saran</a></p>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-striped table-sm" id="myTable">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Kritik dan Saran</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                    {{-- <th scope="col">Header</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($suggestion as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->name_s }}</td>
                        <td>{{ $s->category_s }}</td>
                        <td>{{ $s->critic }} </td>
                        <td><a class="btn btn-warning" href="{{ route('suggestion.edit', $s->id) }}"><i class="fas fa-pen"></i></a></td>
                        <td>
                            <form onsubmit="return confirm('Apakah anda yakin?');"
                                action="{{ route('suggestion.destroy', $s->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit"><i class="fas fa-trash"></i></button>
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
