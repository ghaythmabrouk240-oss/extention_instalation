@extends('layouts.app')

@section('title', 'Liste des Installations')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Liste des Installations</h1>
        <p class="breadcrumb">GMAO > Installations</p>
    </div>
    <div>
        @if($currentUser->canManageInstallations())
            <a href="{{ route('installations.create') }}" class="btn btn-gmao-primary">
                <i class="fa-solid fa-plus me-2"></i>Nouvelle Installation
            </a>
        @endif
    </div>
</div>

<form method="GET" action="{{ route('installations.index') }}" class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Profil</label>
                <select name="profil" class="form-select">
                    <option value="">Tous</option>
                    <option value="IRM" @selected(request('profil') === 'IRM')>IRM</option>
                    <option value="CATHETERISME" @selected(request('profil') === 'CATHETERISME')>Cathétérisme</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    @foreach(\App\Services\InstallationStatusService::statuses() as $statut)
                        <option value="{{ $statut }}" @selected(request('statut') === $statut)>{{ $statut }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Client ID</label>
                <input type="number" name="client_id" value="{{ request('client_id') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Équipement principal</label>
                <select name="equipement_principal_id" class="form-select">
                    <option value="">Tous</option>
                    @foreach($equipements as $equipement)
                        <option value="{{ $equipement->id }}" @selected((string) request('equipement_principal_id') === (string) $equipement->id)>
                            {{ $equipement->code }} - {{ $equipement->designation }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="documents_manquants" value="1" id="documents_manquants" @checked(request()->boolean('documents_manquants'))>
                    <label class="form-check-label" for="documents_manquants">Docs bloquants absents</label>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tri</label>
                <select name="sort" class="form-select">
                    <option value="recent" @selected(request('sort', 'recent') === 'recent')>Plus récent</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Plus ancien</option>
                    <option value="criticite_desc" @selected(request('sort') === 'criticite_desc')>Criticité décroissante</option>
                    <option value="criticite_asc" @selected(request('sort') === 'criticite_asc')>Criticité croissante</option>
                    <option value="nom_az" @selected(request('sort') === 'nom_az')>Nom A-Z</option>
                    <option value="nom_za" @selected(request('sort') === 'nom_za')>Nom Z-A</option>
                    <option value="code_az" @selected(request('sort') === 'code_az')>Code A-Z</option>
                    <option value="code_za" @selected(request('sort') === 'code_za')>Code Z-A</option>
                    <option value="profil" @selected(request('sort') === 'profil')>Profil</option>
                    <option value="statut_cycle" @selected(request('sort') === 'statut_cycle')>Cycle statut</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-gmao-primary">Filtrer</button>
            </div>
            <div class="col-md-1 d-grid">
                <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Profil</th>
                    <th>Statut</th>
                    <th>Criticité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($installations as $installation)
                <tr>
                    <td><strong>{{ $installation->code_installation }}</strong></td>
                    <td>{{ $installation->nom }}</td>
                    <td>
                        @if($installation->type_profil === 'IRM')
                            <span class="badge badge-profil-irm">IRM</span>
                        @else
                            <span class="badge badge-profil-cathlab">CATHETERISME</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = 'bg-secondary';
                            switch($installation->statut) {
                                case 'Brouillon': $badgeClass = 'badge-brouillon'; break;
                                case 'En validation': $badgeClass = 'badge-en-validation'; break;
                                case 'Installé': $badgeClass = 'badge-installe'; break;
                                case 'Opérationnel': $badgeClass = 'badge-operationnel'; break;
                                case 'En maintenance': $badgeClass = 'badge-en-maintenance'; break;
                                case 'Temporairement indisponible': $badgeClass = 'badge-indisponible'; break;
                                case 'Archivé': $badgeClass = 'badge-archive'; break;
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $installation->statut }}</span>
                    </td>
                    <td>{{ $installation->criticite ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('installations.show', $installation) }}" class="btn btn-action btn-view" title="Voir"><i class="fa-solid fa-eye"></i></a>
                        @if($currentUser->canManageInstallations())
                            <a href="{{ route('installations.edit', $installation) }}" class="btn btn-action btn-edit" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                        @endif
                        @if($currentUser->isAdmin())
                            <form action="{{ route('installations.destroy', $installation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette installation ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-action btn-delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Aucune installation trouvée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
