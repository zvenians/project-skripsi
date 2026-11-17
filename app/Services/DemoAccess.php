<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DemoAccess
{
    public function enter(Request $request): void
    {
        if (! config('demo.enabled')) {
            return;
        }

        // All visitors share a dedicated demo identity, never an existing staff account.
        $user = User::query()->firstOrCreate(
            ['email' => config('demo.email')],
            [
                'name' => config('demo.name'),
                'password' => Hash::make(Str::random(64)),
                'is_admin' => true,
            ]
        );

        // Keep the showcase account intact if it was present before this visit.
        if (! $user->is_admin) {
            $user->forceFill(['is_admin' => true])->save();
        }

        if (Auth::id() !== $user->getKey()) {
            Auth::login($user);
            $request->session()->regenerate();
        }
    }
}
