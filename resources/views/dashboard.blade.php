@extends('layouts.app')

@section('title', 'Tableau de bord Installations')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Tableau de bord Installations</h1>
        <p class="breadcrumb">GMAO > KPIs Installations</p>
    </div>
    @if($currentUser->canManageInstallations())
        <a href="{{ route('installations.create') }}" class="btn btn-gmao-primary">
            <i class="fa-solid fa-plus me-2"></i>Nouvelle Installation
        </a>
    @endif
</div>

<div class="alert alert-info alert-gmao">
    <i class="fa-solid fa-user-shield me-2"></i>
    <strong>Rôle actif: {{ ucfirst($currentUser->role) }}.</strong>
    {{ $currentUser->installationPermissionSummary() }}
</div>

@if($canViewStrategicKpis)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-primary">KPIs stratégiques</h5>
        <span class="badge bg-primary">Visible Admin / Manager</span>
    </div>
    <div class="row mb-4">
        @foreach($strategicKpis as $kpi)
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid {{ $kpi['icon'] }}"></i></div>
                    <div class="stat-value">{{ $kpi['value'] }}</div>
                    <div class="stat-label">{{ $kpi['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-primary">Budget par devise</h5>
        <span class="badge bg-primary">EUR / TND</span>
    </div>
    <div class="row mb-4">
        @foreach($budgetByCurrency as $currency => $totals)
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $currency === 'TND' ? 'Dinar tunisien' : 'Euro' }}</span>
                        <span class="badge bg-secondary">{{ $currency }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted small">Budget prevu</div>
                                <strong>{{ number_format($totals['budget_prevu'], 2) }} {{ $currency }}</strong>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Total frais</div>
                                <strong>{{ number_format($totals['total_frais'], 2) }} {{ $currency }}</strong>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Penalites</div>
                                <strong>{{ number_format($totals['total_penalites'], 2) }} {{ $currency }}</strong>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Total final</div>
                                <strong>{{ number_format($totals['total_final'], 2) }} {{ $currency }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning alert-gmao">
        <i class="fa-solid fa-eye-slash me-2"></i>
        Les KPIs stratégiques globaux sont masqués pour le rôle biomédical. Les indicateurs ci-dessous restent orientés action terrain.
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-primary">KPIs opérationnels</h5>
    <span class="badge bg-secondary">Visible rôles internes</span>
</div>
<div class="row mb-4">
    @foreach($operationalKpis as $kpi)
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid {{ $kpi['icon'] }}"></i></div>
                <div class="stat-value">{{ $kpi['value'] }}</div>
                <div class="stat-label">{{ $kpi['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @if($canViewStrategicKpis)
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header">Répartition par statut</div>
                <div class="card-body p-0">
                    <table class="table table-gmao mb-0">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statusCounts as $statut => $total)
                                <tr>
                                    <td>{{ $statut }}</td>
                                    <td><strong>{{ $total }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Aucune installation disponible.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="{{ $canViewStrategicKpis ? 'col-md-7' : 'col-md-12' }} mb-4">
        <div class="card">
            <div class="card-header">Installations récentes</div>
            <div class="card-body p-0">
                <table class="table table-gmao mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Profil</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInstallations as $installation)
                            <tr>
                                <td><strong>{{ $installation->code_installation }}</strong></td>
                                <td>{{ $installation->nom }}</td>
                                <td>{{ $installation->type_profil }}</td>
                                <td>{{ $installation->statut }}</td>
                                <td>
                                    <a href="{{ route('installations.show', $installation) }}" class="btn btn-action btn-view">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune installation disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
