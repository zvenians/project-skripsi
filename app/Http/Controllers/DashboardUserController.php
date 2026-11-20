<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = User::latest()->get();

        return view('dashboard.user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        return view('dashboard/user/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'hp' => 'required',
        ]);
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'no_hp' => $request->hp,
        ]);

        return redirect('dashboard/user')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(User $user)
    {

        return view('dashboard.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        abort_if($this->isProtectedDemoUser($user), 403, 'Akun demo dilindungi.');
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => 'required',
        ]);
        $user = User::whereId($user->id)->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'no_hp' => $request->no_hp,
            // 'password' =>$request->input('password')
        ]);

        return redirect('dashboard/user')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(User $user)
    {
        abort_if($this->isProtectedDemoUser($user), 403, 'Akun demo dilindungi.');
        $user->delete();

        return redirect('dashboard/user')->with('success', 'Data berhasil dihapus');
    }

    private function isProtectedDemoUser(User $user): bool
    {
        $emails = collect(config('demo.profiles', []))->pluck('email');

        return config('demo.enabled') && $emails->contains($user->email);
    }
}
