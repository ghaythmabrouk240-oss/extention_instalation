@extends('layouts.app')

@section('title', 'Liste des Installations')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Liste des Installations</h1>
        <p class="breadcrumb">GMAO > Installations</p>
    </div>
    <div>
        <a href="{{ route('installations.create') }}" class="btn btn-gmao-primary">
            <i class="fa-solid fa-plus me-2"></i>Nouvelle Installation
        </a>
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
                        <a href="{{ route('installations.edit', $installation) }}" class="btn btn-action btn-edit" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('installations.destroy', $installation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette installation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                        </form>
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
