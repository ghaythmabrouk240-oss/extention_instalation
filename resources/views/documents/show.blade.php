@extends('layouts.app')

@section('title', 'Document')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ $document->categorie }} v{{ $document->version }}</h1>
        <p class="breadcrumb"><a href="{{ route('documents.index') }}">Documents</a> > Detail</p>
    </div>
    <a href="{{ route('documents.edit', $document) }}" class="btn btn-gmao-warning">Modifier</a>
</div>

<div class="card">
    <div class="card-body">
        <ul class="detail-list">
            <li><span class="detail-label">Installation</span><span class="detail-value">{{ $document->installation->code_installation }} - {{ $document->installation->nom }}</span></li>
            <li><span class="detail-label">Categorie</span><span class="detail-value">{{ $document->categorie }}</span></li>
            <li><span class="detail-label">Type rapport</span><span class="detail-value">{{ $document->type_rapport ?? '-' }}</span></li>
            <li><span class="detail-label">Profil concerne</span><span class="detail-value">{{ $document->profil_concerne }}</span></li>
            <li><span class="detail-label">Statut</span><span class="detail-value">{{ $document->statut }}</span></li>
            <li><span class="detail-label">Details</span><span class="detail-value">{{ $document->description ?? '-' }}</span></li>
            <li><span class="detail-label">Reference DMS</span><span class="detail-value">{{ $document->reference_dms ?? '-' }}</span></li>
            <li><span class="detail-label">Reference fichier</span><span class="detail-value">{{ $document->reference_fichier ?? '-' }}</span></li>
            <li>
                <span class="detail-label">Fichier</span>
                <span class="detail-value">
                    @if($document->fichier_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($document->fichier_path) }}" target="_blank">{{ $document->fichier_original_name ?? 'Ouvrir le fichier' }}</a>
                    @else
                        -
                    @endif
                </span>
            </li>
            <li><span class="detail-label">Version active</span><span class="detail-value">{{ $document->est_version_active ? 'Oui' : 'Non' }}</span></li>
            <li><span class="detail-label">Bloquant</span><span class="detail-value">{{ $document->est_bloquant ? 'Oui' : 'Non' }}</span></li>
        </ul>
    </div>
</div>
@endsection
