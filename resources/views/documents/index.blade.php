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
                    <th>Catégorie</th>
                    <th>Version</th>
                    <th>Statut</th>
                    <th>Bloquant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->installation->code_installation }}</td>
                    <td>{{ $doc->categorie }}</td>
                    <td>{{ $doc->version }}</td>
                    <td>{{ $doc->statut }}</td>
                    <td>{{ $doc->est_bloquant ? 'Oui' : 'Non' }}</td>
                    <td>
                        <a href="{{ route('documents.show', $doc) }}" class="btn btn-action btn-view">Voir</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
