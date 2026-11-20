<?php

namespace App\Services;

use App\Models\FacilityManager;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DemoAccess
{
    public function enter(Request $request, ?string $requestedRole = null): void
    {
        if (! config('demo.enabled')) {
            return;
        }

        $role = $requestedRole ?? $request->session()->get('demo_role', config('demo.default_role'));
        abort_unless(array_key_exists($role, config('demo.profiles')), 404);
        $profile = config("demo.profiles.$role");
        $request->session()->put('demo_role', $role);

        $organizationId = $role === 'organization'
            ? Organization::query()->firstOrCreate(
                ['code' => 'DEMO-ORG'],
                ['name' => 'Organisasi Mahasiswa Demo'],
            )->getKey()
            : null;
        $managerId = $role === 'manager'
            ? FacilityManager::query()->firstOrCreate(['name' => 'Pengelola Fasilitas Demo'])->getKey()
            : null;

        $user = User::query()->firstOrCreate(
            ['email' => $profile['email']],
            [
                'name' => $profile['name'],
                'password' => Hash::make(Str::random(64)),
                'is_admin' => $role === 'superadmin',
                'role' => $role,
                'organization_id' => $organizationId,
                'facility_manager_id' => $managerId,
            ]
        );

        $user->forceFill([
            'role' => $role,
            'is_admin' => $role === 'superadmin',
            'organization_id' => $organizationId,
            'facility_manager_id' => $managerId,
        ])->save();

        if (Auth::id() !== $user->getKey()) {
            Auth::login($user);
            $request->session()->regenerate();
        }
    }
}
