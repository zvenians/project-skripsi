<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suggestion = Suggestion::latest()->get();
        return view('kritik', compact('suggestion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tambah');
        // return view('dashboard/suggestion/create');
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
            'name_s'    =>  'required',
            'critic'    =>  'required'
        ]);
        DB::table('suggestions')->insert([
                'name_s' => $request->name_s,
                'category_s' =>$request->category_s,
                'critic'=> $request->critic
        ]);
        //  suggestion::create([
        // ]);
        return redirect('kritik')->with('sucess','Kritik berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function show(suggestion $suggestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function edit(suggestion $suggestion)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, suggestion $suggestion)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(suggestion $suggestion)
    {

    }
}