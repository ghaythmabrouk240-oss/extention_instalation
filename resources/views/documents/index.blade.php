@extends('layouts.app')

@section('title', 'Liste des Documents')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Documents</h1>
        <p class="breadcrumb">GMAO > Documents</p>
    </div>
    <div>
        <a href="{{ route('documents.create') }}" class="btn btn-gmao-primary">Nouveau Document</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Installation</th>
                    <th>Categorie</th>
                    <th>Type rapport</th>
                    <th>Profil</th>
                    <th>Version</th>
                    <th>Statut</th>
                    <th>Reference</th>
                    <th>Fichier</th>
                    <th>Actif</th>
                    <th>Bloquant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td>{{ $doc->installation->code_installation }}</td>
                    <td>{{ $doc->categorie }}</td>
                    <td>{{ $doc->type_rapport ?? '-' }}</td>
                    <td>{{ $doc->profil_concerne }}</td>
                    <td>{{ $doc->version }}</td>
                    <td>{{ $doc->statut }}</td>
                    <td>{{ $doc->reference_dms ?? $doc->reference_fichier ?? '-' }}</td>
                    <td>
                        @if($doc->fichier_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->fichier_path) }}" target="_blank">{{ $doc->fichier_original_name ?? 'Ouvrir' }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $doc->est_version_active ? 'Oui' : 'Non' }}</td>
                    <td>{{ $doc->est_bloquant ? 'Oui' : 'Non' }}</td>
                    <td>
                        <a href="{{ route('documents.show', $doc) }}" class="btn btn-action btn-view">Voir</a>
                        <a href="{{ route('documents.edit', $doc) }}" class="btn btn-action btn-edit">Editer</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4">Aucun document trouve.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
