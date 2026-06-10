<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GMAO Healthcare')</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/gmao.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header p-3 text-white text-center border-bottom border-light border-opacity-25">
            <h4 class="mb-0"><i class="fa-solid fa-hospital-user me-2"></i>GMAO Philips</h4>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item sidebar-section">Menu Principal</li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-gauge me-2"></i> Tableau de bord
                </a>
            </li>

            <li class="nav-item sidebar-section mt-3">Activités</li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('installations*') ? 'active' : '' }}" href="{{ route('installations.index') }}">
                    <i class="fa-solid fa-server me-2"></i> Installations
                </a>
            </li>

            <li class="nav-item sidebar-section mt-3">Gestion des Équipements</li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('equipements*') ? 'active' : '' }}" href="{{ route('equipements.index') }}">
                    <i class="fa-solid fa-microchip me-2"></i> Équipements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('sous-equipements*') ? 'active' : '' }}" href="{{ route('sous-equipements.index') }}">
                    <i class="fa-solid fa-memory me-2"></i> Sous-Équipements
                </a>
            </li>

            <li class="nav-item sidebar-section mt-3">Documentation</li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('documents*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                    <i class="fa-solid fa-file-pdf me-2"></i> Documents
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar d-flex justify-content-between align-items-center mb-4">
            <div>
                @php
                    $demoRole = auth()->user()->role ?? session('demo_role', \App\Models\User::ROLE_BIOMEDICAL);
                    $roleLabel = [
                        \App\Models\User::ROLE_ADMIN => 'Administrateur',
                        \App\Models\User::ROLE_BIOMEDICAL => 'Biomédical',
                        \App\Models\User::ROLE_MANAGER => 'Manager',
                    ][$demoRole] ?? ucfirst($demoRole);
                @endphp
                <h5 class="mb-0 text-muted">Bonjour Utilisateur</h5>
                <small class="text-muted">Rôle actif: <strong>{{ $roleLabel }}</strong></small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">POC rôles</span>
                <a class="btn btn-sm {{ $demoRole === \App\Models\User::ROLE_BIOMEDICAL ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ request()->fullUrlWithQuery(['role' => 'biomedical']) }}">Biomédical</a>
                <a class="btn btn-sm {{ $demoRole === \App\Models\User::ROLE_ADMIN ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ request()->fullUrlWithQuery(['role' => 'admin']) }}">Admin</a>
                <a class="btn btn-sm {{ $demoRole === \App\Models\User::ROLE_MANAGER ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ request()->fullUrlWithQuery(['role' => 'manager']) }}">Manager</a>
                <i class="fa-solid fa-user-circle fa-2x text-muted"></i>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-gmao alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-gmao alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Content -->
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
