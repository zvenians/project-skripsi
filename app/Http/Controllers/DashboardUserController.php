<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboarduserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::latest()->get();
        return view('dashboard.user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('dashboard/user/create');
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
            'name'  =>  'required',
            'email' =>  'required|email|unique:users,email',
            'password'  =>  'required|min:8',
            'hp'    =>  'required'
        ]);
        $user = user::create([
            'name' => $request->input('name'),
            'email' =>$request->input('email'),
            'password'=> Hash::make($request->input('password')),
            'no_hp' =>  $request->hp
        ]);
        return redirect('dashboard/user')->with('success','Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(user $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(user $user)
    {

        return view('dashboard.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, user $user)
    {
        abort_if(config('demo.enabled') && $user->email === config('demo.email'), 403, 'Akun demo dilindungi.');
        $request->validate([
            'name'  =>  'required',
            'email' =>  ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp'  =>  'required'
        ]);
        $user = User::whereId($user->id)->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'no_hp' =>  $request->no_hp
            // 'password' =>$request->input('password')
        ]);
        return redirect('dashboard/user')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(user $user)
    {
        abort_if(config('demo.enabled') && $user->email === config('demo.email'), 403, 'Akun demo dilindungi.');
        $user = user::find($user->id);
        $user->delete();

        return redirect('dashboard/user')->with('success','Data berhasil dihapus');
    }
}