<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Order;
use App\Models\User;
use App\Services\BookingConflictService;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $order = $this->scopedQuery()->latest()->paginate(20)->withQueryString();
        $news = DB::table('news')->get();
        $id = Auth::user()->is_admin;
        $name = Auth::user()->name;

        return view('dashboard.order.index', compact('order', 'id', 'news', 'name'));
    }

    public function kategori($id)
    {
        $status = match ($id) {
            'terima' => 2, 'tolak' => 1, 'pending' => null, default => 'all'
        };
        $order = $this->scopedQuery()->when($status !== 'all', fn ($query) => is_null($status) ? $query->whereNull('status_order') : $query->where('status_order', $status))->latest()->paginate(20)->withQueryString();
        $news = DB::table('news')->get();
        $id = Auth::user()->is_admin;
        $name = Auth::user()->name;

        return view('dashboard.order.index', compact('order', 'id', 'news', 'name'));
    }

    public function terima($id, WhatsAppNotifier $notifier, BookingConflictService $bookings)
    {
        $order = Order::query()->with(['user', 'facility'])->findOrFail($id);
        $this->authorizeDecision($order);
        $order = $bookings->approve($order, 'Disetujui pengelola fasilitas.');
        $notifier->send($order->user?->no_hp, 'Pemesanan fasilitas UNAI disetujui: '.($order->facility?->title ?? 'Fasilitas'));

        return redirect('/dashboard/order')->with('status', 'Pesanan berhasil disetujui.');
    }

    public function kirim_pesan_pemesanan_terima(Request $request, $id, BookingConflictService $bookings)
    {
        $request->validate([
            'pesan' => 'required|string|max:2000',
            'status' => 'required|integer|in:1,2',
        ]);
        $order = Order::query()->with('facility')->findOrFail($id);
        $this->authorizeDecision($order);
        if ((int) $request->status === 2) {
            $bookings->approve($order, $request->pesan);
        } else {
            $order->forceFill(['status_order' => 1, 'pesan' => $request->pesan, 'decision_reason' => $request->pesan])->save();
        }

        return redirect('dashboard/order')->with('status', 'Data Berhasil Di Update');
    }

    public function kirim_pesan_pemesanan(Request $request, BookingConflictService $bookings)
    {
        $id = $request->validate(['id' => 'required|integer|exists:orders,id'])['id'];
        $request->validate([
            'pesan' => 'required|string|max:2000',
            'status' => 'required|integer|in:1,2',
        ]);
        $order = Order::query()->with('facility')->findOrFail($id);
        $this->authorizeDecision($order);
        if ((int) $request->status === 2) {
            $bookings->approve($order, $request->pesan);
        } else {
            $order->forceFill(['status_order' => 1, 'pesan' => $request->pesan, 'decision_reason' => $request->pesan])->save();
        }

        return redirect('dashboard/order')->with('status', 'Data Berhasil Di Update');
    }

    public function tolak($id)
    {
        $order = Order::query()->with('facility')->findOrFail($id);
        $this->authorizeDecision($order);
        $order->forceFill(['status_order' => 1, 'decision_reason' => 'Ditolak pengelola fasilitas.'])->save();

        return redirect('dashboard/order')->with('status', 'Data Berhasil Di Update');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $user = auth()->user()->is_admin ? User::all() : User::whereKey(auth()->id())->get();
        $facility = Facility::get();
        $order = Order::where('user_id', auth()->id())->get();

        return view('dashboard/order/create', compact('facility', 'user', 'order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request, BookingConflictService $bookings)
    {
        $validated = $request->validate([
            'detil' => 'required|string|max:255',
            'facility_id' => 'required|exists:facilities,id',
            'jam' => 'required|date',
            'end' => 'required|date|after:jam',
        ]);
        $bookings->create([
            'user_id' => auth()->id(),
            'detil' => $validated['detil'],
            'facility_id' => $validated['facility_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'date_end' => $request->jam,
            'pesan' => null,
            'selesai' => $request->end,
            'organization_id' => auth()->user()->organization_id,
        ]);

        return redirect('dashboard/order')->with('sucess', 'Data berhasil disimpan!');

    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Order  $order
     * @return Response
     */
    public function edit($order)
    {
        $user = User::get();
        // $order = $order;
        $facility = Facility::get();
        $data = Order::query()->findOrFail($order);
        abort_unless(auth()->user()->is_admin || (int) $data->user_id === auth()->id(), 403);
        $pengguna = User::query()->find($data->user_id);

        return view('dashboard/order/edit', compact('user', 'facility', 'data', 'order', 'pengguna'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Order  $order
     * @return Response
     */
    public function update(Request $request, $order, BookingConflictService $bookings)
    {
        $request->validate([
            'detil' => 'required|string|max:255',
            'facility_id' => 'required|exists:facilities,id',
            'jam' => 'required|date',
            'end' => 'required|date|after:jam',
        ]);
        $existing = Order::query()->findOrFail($order);
        abort_unless(auth()->user()->is_admin || (int) $existing->user_id === auth()->id(), 403);
        $bookings->update($existing, [
            'detil' => $request->detil,
            'user_id' => auth()->user()->is_admin ? (int) $existing->user_id : auth()->id(),
            'facility_id' => $request->facility_id,
            'date_end' => $request->jam,
            'selesai' => $request->end,
        ]);

        return redirect('dashboard/order')->with('sucess', 'Data Berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Order  $order
     * @return Response
     */
    public function destroy($order)
    {
        // $order = Order::find($order->id);
        // $order->delete();
        $existing = Order::query()->findOrFail($order);
        abort_unless(auth()->user()->is_admin || (int) $existing->user_id === auth()->id(), 403);
        $existing->delete();

        return redirect('dashboard/order')->with('sucess', 'Data Berhasil Hapus!');
    }

    private function scopedQuery()
    {
        $user = Auth::user();

        return Order::query()->with(['facility.manager', 'user', 'organization'])
            ->when($user->role === 'organization', fn ($query) => $query->where('organization_id', $user->organization_id))
            ->when($user->role === 'manager', fn ($query) => $query->whereHas('facility', fn ($facility) => $facility->where('manager_id', $user->facility_manager_id)))
            ->when(! $user->is_admin && ! in_array($user->role, ['organization', 'manager'], true), fn ($query) => $query->where('user_id', $user->id));
    }

    private function authorizeDecision(Order $order): void
    {
        $user = Auth::user();
        abort_unless(
            $user->role === 'superadmin'
            || ($user->role === 'manager'
                && $user->facility_manager_id !== null
                && (int) $order->facility?->manager_id === (int) $user->facility_manager_id),
            403,
        );
    }
}
