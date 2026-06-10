<?php

namespace App\Policies;

use App\Models\Installation;
use App\Models\User;

class InstallationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewInstallationKpis();
    }

    public function view(User $user, Installation $installation): bool
    {
        return $user->canViewInstallationKpis();
    }

    public function viewDashboard(User $user): bool
    {
        return $user->canViewInstallationKpis();
    }

    public function viewStrategicKpis(User $user): bool
    {
        return $user->canViewStrategicInstallationKpis();
    }

    public function create(User $user): bool
    {
        return $user->canManageInstallations();
    }

    public function update(User $user, Installation $installation): bool
    {
        return $user->canManageInstallations();
    }

    public function changeStatus(User $user, Installation $installation): bool
    {
        return $user->canManageInstallations();
    }

    public function delete(User $user, Installation $installation): bool
    {
        return $user->isAdmin();
    }
}
