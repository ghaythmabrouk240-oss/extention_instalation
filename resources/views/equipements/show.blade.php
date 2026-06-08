@extends('layouts.app')

@section('title', 'Détail Équipement')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ $equipement->designation }} ({{ $equipement->code }})</h1>
        <p class="breadcrumb"><a href="{{ route('equipements.index') }}">Équipements</a> > Détail</p>
    </div>
    <div>
        <a href="{{ route('equipements.edit', $equipement) }}" class="btn btn-gmao-warning me-2">Modifier</a>
        <a href="{{ route('equipements.index') }}" class="btn btn-gmao-secondary">Retour</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="mb-3 text-primary">Détails Techniques</h4>
        <ul class="detail-list">
            <li><span class="detail-label">Numéro</span><span class="detail-value">{{ $equipement->numero_equipement }}</span></li>
            <li><span class="detail-label">Marque</span><span class="detail-value">{{ $equipement->marque }}</span></li>
            <li><span class="detail-label">Modèle</span><span class="detail-value">{{ $equipement->modele }}</span></li>
            <li><span class="detail-label">Numéro Série</span><span class="detail-value">{{ $equipement->numero_serie }}</span></li>
            <li><span class="detail-label">Client ID</span><span class="detail-value">{{ $equipement->client_id }}</span></li>
        </ul>
    </div>
</div>
@endsection
