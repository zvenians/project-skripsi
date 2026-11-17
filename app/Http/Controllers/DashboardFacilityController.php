<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class DashboardFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facility = Facility::latest()->get();
        return view('dashboard.facility.index', compact('facility'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard/facility/create');
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
            'img'   =>  'required|image|mimes:png,jpg,jpeg,webp|max:5120',
            'title' =>  'required|string|max:150',
            'desc'  =>  'required|string|max:255',
        ]);
        $gambar = \Illuminate\Support\Str::uuid() . '.' . $request->file('img')->extension();
        $request->img->move(public_path('storage/'), $gambar);
        // $image = $request->file('image')->store('facility-images');
        $facility = Facility::create([
            'image' => $gambar,
            'title' => $request->input('title'),
            'category' =>$request->input('category'),
            'desc'=> $request->input('desc')
        ]);
        return redirect('dashboard/facility')->with('success','Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function show(Facility $facility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function edit(Facility $facility)
    {
        return view('dashboard.facility.edit', compact('facility'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facility $facility)
    {
        // $image = $request->file('image')->update('facility-images');
        $request->validate([
            'img'   =>  'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'title' =>  'required|string|max:150',
            'desc'  =>  'required'
        ]);
        if(!$request->file('img')) {
            $gambar = $facility->image;
        } else {
            $gambar = \Illuminate\Support\Str::uuid() . '.' . $request->file('img')->extension();
            $request->img->move(public_path('storage/'), $gambar);
        }
        $facility = Facility::whereId($facility->id)->update([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'image' =>$gambar,
            'desc' =>$request->input('desc')
        ]);
        return redirect('dashboard/facility')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facility $facility)
    {
        $facility = Facility::find($facility->id);
        $facility->delete();

        return redirect('dashboard/facility')->with('success','Data berhasil dihapus!');
    }
}