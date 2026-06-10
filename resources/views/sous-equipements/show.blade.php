@extends('layouts.app')

@section('title', 'Detail Sous-Equipement')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ $sousEquipement->designation }}</h1>
        <p class="breadcrumb"><a href="{{ route('sous-equipements.index') }}">Sous-equipements</a> > {{ $sousEquipement->identifiant }}</p>
    </div>
    <div>
        <a href="{{ route('sous-equipements.edit', $sousEquipement) }}" class="btn btn-gmao-warning me-2">
            <i class="fa-solid fa-pen me-2"></i>Modifier
        </a>
        <a href="{{ route('sous-equipements.index') }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="detail-list">
            <li><span class="detail-label">Identifiant</span><span class="detail-value">{{ $sousEquipement->identifiant }}</span></li>
            <li><span class="detail-label">Designation</span><span class="detail-value">{{ $sousEquipement->designation }}</span></li>
            <li><span class="detail-label">Marque</span><span class="detail-value">{{ $sousEquipement->marque }}</span></li>
            <li><span class="detail-label">Modele</span><span class="detail-value">{{ $sousEquipement->modele }}</span></li>
            <li><span class="detail-label">Equipement parent</span><span class="detail-value">{{ $sousEquipement->equipement?->code ?? '-' }} - {{ $sousEquipement->equipement?->designation ?? '' }}</span></li>
            <li><span class="detail-label">Description</span><span class="detail-value">{{ $sousEquipement->description }}</span></li>
        </ul>
    </div>
</div>
@endsection
