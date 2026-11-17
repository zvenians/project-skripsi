@extends('dashboard.layouts.main')
@section('title', 'Pesanan | Fasilitas UNAI')
@section('container')
<section class="pt-4" data-motion>
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
        <div><h1 class="h2 unai-page-heading mb-1">Pesanan</h1><p class="text-secondary mb-0">Kelola pemesanan fasilitas Universitas Advent Indonesia.</p></div>
        <a class="btn btn-primary" href="{{ url('order/create') }}"><i class="fas fa-plus me-2" aria-hidden="true"></i>Tambah Pesanan</a>
    </div>
    @foreach(['sucess' => 'success', 'success' => 'success', 'status' => 'success'] as $key => $level)
        @if(session($key))<div class="alert alert-{{ $level }}" role="status">{{ session($key) }}</div>@endif
    @endforeach
    <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="Filter pesanan">
        <a class="btn btn-sm btn-outline-secondary" href="{{ url('dashboard/kategori/all') }}">Semua</a>
        <a class="btn btn-sm btn-outline-success" href="{{ url('dashboard/kategori/terima') }}">Diterima</a>
        <a class="btn btn-sm btn-outline-danger" href="{{ url('dashboard/kategori/tolak') }}">Ditolak</a>
        <a class="btn btn-sm btn-outline-secondary" href="{{ url('dashboard/kategori/belum_dikonfirmasi') }}">Menunggu</a>
    </div>
    <div class="unai-surface p-2 p-md-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr>
                    <th scope="col">No.</th><th scope="col">Pemesan</th><th scope="col">Tujuan</th><th scope="col">Tempat</th><th scope="col">Waktu Mulai</th><th scope="col">Status</th><th scope="col" class="text-end">Tindakan</th>
                </tr></thead>
                <tbody>
                @forelse($order as $o)
                    <tr>
                        <td>{{ $order->firstItem() + $loop->index }}</td>
                        <td>{{ $o->user?->name ?? $o->user_id }}</td>
                        <td>{{ $o->detil }}</td>
                        <td>{{ $o->facility?->title ?? 'Fasilitas dihapus' }}</td>
                        <td>{{ $o->date_end ? date('d/m/Y H:i', strtotime($o->date_end)) : '-' }}</td>
                        <td>
                            @if($o->status_order == 2)<span class="badge text-bg-success">Diterima</span>
                            @elseif($o->status_order == 1)<span class="badge text-bg-danger">Ditolak</span>
                            @else<span class="badge text-bg-warning">Menunggu</span>@endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewOrder{{ $o->id }}" aria-label="Lihat pesanan {{ $o->id }}"><i class="fas fa-eye" aria-hidden="true"></i></button>
                            <a class="btn btn-sm btn-outline-warning" href="{{ url('order/edit/' . $o->id) }}" aria-label="Edit pesanan {{ $o->id }}"><i class="fas fa-pen" aria-hidden="true"></i></a>
                            <form action="{{ url('order/destroy/' . $o->id) }}" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus pesanan {{ $o->id }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada pesanan pada kategori ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($order->hasPages())<div class="mt-3">{{ $order->links() }}</div>@endif
    @foreach($order as $o)
        <div class="modal fade" id="viewOrder{{ $o->id }}" tabindex="-1" aria-labelledby="viewOrderTitle{{ $o->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title fs-5" id="viewOrderTitle{{ $o->id }}">Detail Pesanan #{{ $o->id }}</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <dl class="row mb-0"><dt class="col-sm-4">Pemesan</dt><dd class="col-sm-8">{{ $o->user?->name ?? $o->user_id }}</dd>
                    <dt class="col-sm-4">Acara</dt><dd class="col-sm-8">{{ $o->detil }}</dd>
                    <dt class="col-sm-4">Fasilitas</dt><dd class="col-sm-8">{{ $o->facility?->title ?? '-' }}</dd>
                    <dt class="col-sm-4">Mulai</dt><dd class="col-sm-8">{{ $o->date_end }}</dd>
                    <dt class="col-sm-4">Selesai</dt><dd class="col-sm-8">{{ $o->selesai }}</dd>
                    <dt class="col-sm-4">Pesan</dt><dd class="col-sm-8">{{ $o->pesan ?: '-' }}</dd></dl>
                    @if(auth()->user()->is_admin)
                        <hr><form action="{{ url('dashboard/kirim_pesan_pemesanan') }}" method="POST" data-disable-on-submit>
                            @csrf <input type="hidden" name="id" value="{{ $o->id }}">
                            <label for="status{{ $o->id }}" class="form-label fw-semibold">Keputusan</label>
                            <select class="form-select mb-3" name="status" id="status{{ $o->id }}" required>
                                <option value="2" {{ $o->status_order == 2 ? 'selected' : '' }}>Terima</option>
                                <option value="1" {{ $o->status_order == 1 ? 'selected' : '' }}>Tolak</option>
                            </select>
                            <label for="pesan{{ $o->id }}" class="form-label fw-semibold">Pesan untuk pemesan</label>
                            <textarea name="pesan" id="pesan{{ $o->id }}" rows="3" maxlength="2000" class="form-control mb-3" required>{{ $o->pesan }}</textarea>
                            <button class="btn btn-primary" type="submit">Simpan Keputusan</button>
                        </form>
                    @endif
                </div>
            </div></div>
        </div>
    @endforeach
</section>
@endsection
