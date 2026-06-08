@extends('layouts.app')

@section('title', 'Liste des Équipements')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Liste des Équipements</h1>
        <p class="breadcrumb">GMAO > Équipements</p>
    </div>
    <div>
        <a href="{{ route('equipements.create') }}" class="btn btn-gmao-primary">
            <i class="fa-solid fa-plus me-2"></i>Nouvel Équipement
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Numéro</th>
                    <th>Modèle</th>
                    <th>Marque</th>
                    <th>Désignation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipements as $equipement)
                <tr>
                    <td><strong>{{ $equipement->code }}</strong></td>
                    <td>{{ $equipement->numero_equipement }}</td>
                    <td>{{ $equipement->modele }}</td>
                    <td>{{ $equipement->marque }}</td>
                    <td>{{ $equipement->designation }}</td>
                    <td>
                        <a href="{{ route('equipements.show', $equipement) }}" class="btn btn-action btn-view" title="Voir"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('equipements.edit', $equipement) }}" class="btn btn-action btn-edit" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('equipements.destroy', $equipement) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Aucun équipement trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
