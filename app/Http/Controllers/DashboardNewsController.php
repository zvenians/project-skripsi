<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class DashboardNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = DB::table('news')->get();
        // $news = News::latest()->get();
        return view('dashboard.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::get();
        $facility = Facility::get();
        $news = news::get();

        return view('dashboard/news/create', compact('facility','user','news'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'desc_n'    =>  'required',
            'time'  =>  'required',
            'category_n'    =>  'required',
        ]);
        // if(!$request->time) {
        //     return redirect('news/create')->with('sucess','Waktu Harus Di Isi');
        // }
        $news = News::create([
          'user_id' => $request->input('user_id'),
          'desc_n' => $request->input('desc_n'),
          'time' => $request->input('time'),
          'category_n' => $request->input('category_n'),
          'category_n' =>$request->input('category_n'),
          'facility_id' => $request->input('facility_id')
        ]);
        return redirect('dashboard/order')->with('sucess','Tempat berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\news  $news
     * @return \Illuminate\Http\Response
     */
    public function show(news $news)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\news  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(news $news)
    {
        $user = User::get();
        $facility = Facility::get();
        // $news = News::get();

        return view('dashboard/news/edit', compact('user','facility','news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\news  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, news $news)
    {
        $request->validate([
            'desc_n'    =>  'required',
            'time'  =>  'required',
            'category_n'    =>  'required',
        ]);
        $news = news::whereId($news->id)->update([
            'user_id' => $request->input('user_id'),
            'desc_n' => $request->input('desc_n'),
            'time' => $request->input('time'),
            'category_n' => $request->input('category_n'),
            'category_n' =>$request->input('category_n'),
            'facility_id' => $request->input('facility_id')
        ]);
        return redirect('dashboard/order')->with('sucess', 'Tempat telah disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\news  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(news $news)
    {
        $news = News::find($news->id);
        $news->delete();

        return redirect('dashboard/order');
    }
}