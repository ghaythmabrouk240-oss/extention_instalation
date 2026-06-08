@extends('layouts.app')

@section('title', 'Nouvel Équipement')

@section('content')
<div class="page-header">
    <h1>Nouvel Équipement</h1>
    <p class="breadcrumb"><a href="{{ route('equipements.index') }}">Équipements</a> > Nouveau</p>
</div>

<form action="{{ route('equipements.store') }}" method="POST">
    @csrf
    <div class="form-section">
        <h3>Informations</h3>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Numéro Équipement <span class="text-danger">*</span></label>
                <input type="text" name="numero_equipement" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Modèle <span class="text-danger">*</span></label>
                <input type="text" name="modele" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Marque <span class="text-danger">*</span></label>
                <input type="text" name="marque" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Désignation <span class="text-danger">*</span></label>
                <input type="text" name="designation" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Numéro Série <span class="text-danger">*</span></label>
                <input type="text" name="numero_serie" class="form-control" required>
            </div>
        </div>
        <!-- Simplified for brevity, normally you'd add the remaining fields -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Client ID <span class="text-danger">*</span></label>
                <input type="number" name="client_id" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Modalité ID <span class="text-danger">*</span></label>
                <input type="number" name="modalite_id" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Software <span class="text-danger">*</span></label>
                <input type="text" name="software" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Date Installation <span class="text-danger">*</span></label>
                <input type="date" name="date_installation" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date Début Garantie <span class="text-danger">*</span></label>
                <input type="date" name="date_debut_garantie" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Plan Prev <span class="text-danger">*</span></label>
                <input type="number" name="plan_prev" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Garantie <span class="text-danger">*</span></label>
                <input type="text" name="garantie" class="form-control" required>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('equipements.index') }}" class="btn btn-gmao-secondary">Annuler</a>
        <button type="submit" class="btn btn-gmao-primary">Enregistrer</button>
    </div>
</form>
@endsection
