<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = Order::query()->with(['facility', 'user'])->when(!Auth::user()->is_admin, fn ($q) => $q->where('user_id', Auth::id()))->latest()->paginate(20);
        $news = DB::table('news')->get();
        $id = Auth::user()->is_admin;
        $name = Auth::user()->name;
        return view('dashboard.order.index', compact('order','id','news','name'));
    }

    public function kategori($id)
    {
        if($id == "all") {
            $order = Order::query()->with(['facility', 'user'])->when(!Auth::user()->is_admin, fn ($q) => $q->where('user_id', Auth::id()))->latest()->paginate(20);
        } elseif($id == "terima") {
            $order = Order::query()->with(['facility', 'user'])->when(!Auth::user()->is_admin, fn ($q) => $q->where('user_id', Auth::id()))->latest()->where('status_order',2)->get();
        } elseif($id == "tolak") {
            $order = Order::query()->with(['facility', 'user'])->when(!Auth::user()->is_admin, fn ($q) => $q->where('user_id', Auth::id()))->latest()->where('status_order',1)->get();
        } else {
            $order = Order::query()->with(['facility', 'user'])->when(!Auth::user()->is_admin, fn ($q) => $q->where('user_id', Auth::id()))->latest()->where('status_order',null)->get();
        }
        $news = DB::table('news')->get();
        $id = Auth::user()->is_admin;
        $name = Auth::user()->name;
        return view('dashboard.order.index', compact('order','id','news','name'));
    }

    public function terima($id, \App\Services\WhatsAppNotifier $notifier)
    {
        $order = Order::query()->with(['user', 'facility'])->findOrFail($id);
        $order->status_order = 2;
        $order->save();
        $notifier->send($order->user?->no_hp, 'Pemesanan fasilitas UNAI disetujui: '.($order->facility?->title ?? 'Fasilitas'));
        return redirect('/dashboard/order')->with('status', 'Pesanan berhasil disetujui.');
    }

    public function kirim_pesan_pemesanan_terima(Request $request,$id)
    {
        $request->validate([
            'pesan' =>  'required|string|max:2000',
            'status' => 'required|integer|in:1,2'
        ]);
        DB::table('orders')->where('id',$id)->update([
            'status_order'  => $request->status,
            'pesan' =>  $request->pesan
        ]);

        return redirect('dashboard/order')->with('status','Data Berhasil Di Update');
    }

    public function kirim_pesan_pemesanan(Request $request)
    {
        $id = $request->validate(['id' => 'required|integer|exists:orders,id'])['id'];
        $request->validate([
            'pesan' =>  'required|string|max:2000',
            'status' => 'required|integer|in:1,2'
        ]);
        DB::table('orders')->where('id',$id)->update([
            'status_order'  => $request->status,
            'pesan' =>  $request->pesan
        ]);

        return redirect('dashboard/order')->with('status','Data Berhasil Di Update');
    }

    public function tolak($id)
    {
        DB::table('orders')->where('id',$id)->update([
            'status_order'  =>  1
        ]);

        return redirect('dashboard/order')->with('status','Data Berhasil Di Update');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user()->is_admin ? User::all() : User::whereKey(auth()->id())->get();
        $facility = Facility::get();
        $order = Order::where('user_id', auth()->id())->get();

        return view('dashboard/order/create', compact('facility','user','order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'detil' => 'required|string|max:255',
            'facility_id' => 'required|exists:facilities,id',
            'jam' => 'required|date',
            'end' => 'required|date|after:jam',
        ]);
        Order::create([
          'user_id' => auth()->id(),
          'detil' => $validated['detil'],
          'facility_id' => $validated['facility_id'],
          'created_at'  =>  date('Y-m-d H:i:s'),
          'date_end' =>  $request->jam,
          'pesan'   =>  null,
          'selesai' =>  $request->end
        ]);
        return redirect('dashboard/order')->with('sucess','Data berhasil disimpan!');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($order)
    {
        $user = User::get();
        // $order = $order;
        $facility = Facility::get();
        $data = Order::query()->findOrFail($order);
        abort_unless(auth()->user()->is_admin || (int) $data->user_id === auth()->id(), 403);
        $pengguna = User::query()->find($data->user_id);

        return view('dashboard/order/edit', compact('user','facility','data','order','pengguna'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order)
    {
        $request->validate([
            'detil' => 'required|string|max:255',
            'facility_id' => 'required|exists:facilities,id',
            'jam' => 'required|date',
            'end' => 'required|date|after:jam'
        ]);
        $existing = Order::query()->findOrFail($order);
        abort_unless(auth()->user()->is_admin || (int) $existing->user_id === auth()->id(), 403);
        DB::table('orders')->where('id',$order)->update([
            'detil' => $request->detil,
            'user_id' => auth()->user()->is_admin ? (int) $existing->user_id : auth()->id(),
            'facility_id' =>$request->facility_id,
            'date_end'  =>  $request->jam,
            'selesai'   =>  $request->end
        ]);
        return redirect('dashboard/order')->with('sucess', 'Data Berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
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
}