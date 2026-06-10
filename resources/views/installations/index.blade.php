@extends('layouts.app')

@section('title', 'Liste des Installations')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Liste des Installations</h1>
        <p class="breadcrumb">GMAO > Installations</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('installations.index', array_merge($filters, ['export' => 1])) }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-file-export me-2"></i>Exporter la liste
        </a>
        <a href="{{ route('installations.calendar') }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-calendar-days me-2"></i>Calendrier
        </a>
        <a href="{{ route('installations.create') }}" class="btn btn-gmao-primary">
            <i class="fa-solid fa-plus me-2"></i>Nouvelle Installation
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('installations.index') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Code ou nom">
            </div>
            <div class="col-md-2">
                <label class="form-label">Profil</label>
                <select name="type_profil" class="form-select">
                    <option value="">Tous</option>
                    <option value="IRM" @selected(($filters['type_profil'] ?? '') === 'IRM')>IRM</option>
                    <option value="CATHETERISME" @selected(($filters['type_profil'] ?? '') === 'CATHETERISME')>Catheterisme</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    @foreach($statuses as $statut)
                        <option value="{{ $statut }}" @selected(($filters['statut'] ?? '') === $statut)>{{ $statut }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Client / site</label>
                <select name="client_id" class="form-select">
                    <option value="">Tous</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(($filters['client_id'] ?? '') == $client->id)>{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documents_manquants" value="1" id="documents_manquants" @checked(!empty($filters['documents_manquants']))>
                    <label class="form-check-label" for="documents_manquants">Docs requis manquants</label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-gmao-primary">Filtrer</button>
                <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">Reinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Profil</th>
                    <th>Client / site</th>
                    <th>Equipement principal</th>
                    <th>Statut</th>
                    <th>Criticite</th>
                    <th>Debut prevu</th>
                    <th>Docs manquants</th>
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
                    <td>{{ $installation->client?->nom ?? '-' }}</td>
                    <td>{{ $installation->equipementPrincipal?->code ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $installation->statut }}</span></td>
                    <td>{{ $installation->criticite ?? 'N/A' }}</td>
                    <td>{{ optional($installation->planned_start_date)->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        @php($missingCount = count($installation->missingRequiredDocumentCategories()))
                        <span class="badge {{ $missingCount ? 'bg-warning text-dark' : 'bg-success' }}" title="{{ $missingCount ? implode(', ', $installation->missingRequiredDocumentCategories()) : 'Complet' }}">{{ $missingCount }}</span>
                    </td>
                    <td>
                        <a href="{{ route('installations.show', $installation) }}" class="btn btn-action btn-view" title="Voir"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('installations.edit', $installation) }}" class="btn btn-action btn-edit" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('installations.destroy', $installation) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette installation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4">Aucune installation trouvee.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
