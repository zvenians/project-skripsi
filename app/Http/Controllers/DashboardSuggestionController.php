<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class DashboardSuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $suggestion = Suggestion::latest()->get();
        return view('dashboard.suggestion.index',compact('suggestion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('/suggestion/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        suggestion::create([
            'name_s' => $request->input('name_s'),
            'category_s' =>$request->input('category_s'),
            'critic'=> $request->input('critic')
        ]);
        return redirect('dashboard/suggestion')->with('success','Data berhasil disimpan!');
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
        return view('dashboard.suggestion.edit', compact('suggestion'));
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
        $request->validate([
            'name_s'    =>  'required',
            'kritik'    =>  'required'
        ]);
         suggestion::whereId($suggestion->id)->update([
            'name_s' => $request->input('name_s'),
            'category_s' => $request->input('category_s'),
            'critic' =>$request->input('kritik')
        ]);
        return redirect('dashboard/suggestion')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(suggestion $suggestion)
    {
        $suggestion = suggestion::find($suggestion->id);
        $suggestion->delete();

        return redirect('dashboard/suggestion')->with('success','Data berhasil dihapus');
    }
}