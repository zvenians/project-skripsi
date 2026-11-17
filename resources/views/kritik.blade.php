@extends('layouts.main')

@section('container')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css">
    <h3 style="padding-top: 1em">Halaman ini merupakan tempat anda memberikan kritik maupun saran terhadap aplikasi ini
        maupun fasilitas yang
        tersedia di Universitas Advent Indonesia</h3>
    <div class="table-responsive" style="padding-top: 2em">
        <p><a class="btn btn-primary" href="{{ url('suggestion/create') }}">Tambah Kritik & Saran</a></p>
        @if (session()->get('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <table class="table table-striped table-sm" id="myTable">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Kritik dan Saran</th>
                    {{-- <th scope="col">Header</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($suggestion as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->name_s }}</td>
                        <td>{{ $s->category_s }}</td>
                        <td>{{ $s->critic }}</td>
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
