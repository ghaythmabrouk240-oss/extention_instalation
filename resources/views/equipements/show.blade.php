@extends('layouts.app')

@section('title', 'Detail Equipement')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ $equipement->designation }} ({{ $equipement->code }})</h1>
        <p class="breadcrumb"><a href="{{ route('equipements.index') }}">Equipements</a> > Detail</p>
    </div>
    <div>
        <a href="{{ route('equipements.edit', $equipement) }}" class="btn btn-gmao-warning me-2">Modifier</a>
        <a href="{{ route('equipements.index') }}" class="btn btn-gmao-secondary">Retour</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h4 class="mb-3 text-primary">Details Techniques</h4>
        <ul class="detail-list">
            <li><span class="detail-label">Numero</span><span class="detail-value">{{ $equipement->numero_equipement }}</span></li>
            <li><span class="detail-label">Marque</span><span class="detail-value">{{ $equipement->marque }}</span></li>
            <li><span class="detail-label">Modele</span><span class="detail-value">{{ $equipement->modele }}</span></li>
            <li><span class="detail-label">Numero Serie</span><span class="detail-value">{{ $equipement->numero_serie }}</span></li>
            <li><span class="detail-label">Client ID</span><span class="detail-value">{{ $equipement->client_id }}</span></li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary">Sous-equipements</h4>
            <a href="{{ route('sous-equipements.create') }}" class="btn btn-gmao-primary btn-sm">Ajouter</a>
        </div>
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Designation</th>
                    <th>Marque</th>
                    <th>Modele</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipement->sousEquipements as $sousEquipement)
                    <tr>
                        <td>{{ $sousEquipement->identifiant }}</td>
                        <td>{{ $sousEquipement->designation }}</td>
                        <td>{{ $sousEquipement->marque }}</td>
                        <td>{{ $sousEquipement->modele }}</td>
                        <td>
                            <a href="{{ route('sous-equipements.show', $sousEquipement) }}" class="btn btn-action btn-view" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('sous-equipements.edit', $sousEquipement) }}" class="btn btn-action btn-edit" title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Aucun sous-equipement rattache.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
