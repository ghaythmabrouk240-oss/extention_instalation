<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
    protected function effectiveUser(): User
    {
        if (auth()->check()) {
            return auth()->user();
        }

        $role = session('demo_role', User::ROLE_BIOMEDICAL);

        return new User([
            'name' => 'Demo '.ucfirst($role),
            'email' => 'demo-'.$role.'@example.invalid',
            'role' => $role,
        ]);
    }

    protected function effectiveUserId(): int
    {
        if (auth()->id()) {
            return auth()->id();
        }

        return User::query()
            ->where('role', $this->effectiveUser()->role)
            ->value('id')
            ?? User::query()->value('id')
            ?? 1;
    }

    protected function authorizeInstallation(string $ability, mixed $arguments): void
    {
        Gate::forUser($this->effectiveUser())->authorize($ability, $arguments);
    }
}
