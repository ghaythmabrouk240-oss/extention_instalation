@extends('layouts.app')

@section('title', 'Nouvelle Installation')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Nouvelle Installation</h1>
        <p class="breadcrumb"><a href="{{ route('installations.index') }}">Installations</a> > Nouvelle</p>
    </div>
</div>

<form action="{{ route('installations.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-section">
                <h3><i class="fa-solid fa-circle-info me-2"></i>Informations Générales</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Code Installation <span class="text-danger">*</span></label>
                        <input type="text" name="code_installation" class="form-control @error('code_installation') is-invalid @enderror" value="{{ old('code_installation') }}" required>
                        @error('code_installation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Type de Profil <span class="text-danger">*</span></label>
                        <select name="type_profil" class="form-select @error('type_profil') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            <option value="IRM" {{ old('type_profil') == 'IRM' ? 'selected' : '' }}>IRM</option>
                            <option value="CATHETERISME" {{ old('type_profil') == 'CATHETERISME' ? 'selected' : '' }}>Cathétérisme</option>
                        </select>
                        @error('type_profil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut initial <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            <option value="Brouillon" {{ old('statut', 'Brouillon') == 'Brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="En validation" {{ old('statut') == 'En validation' ? 'selected' : '' }}>En validation</option>
                            <option value="Installé" {{ old('statut') == 'Installé' ? 'selected' : '' }}>Installé</option>
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Criticité</label>
                        <select name="criticite" class="form-select">
                            <option value="">Sélectionner...</option>
                            <option value="Basse" {{ old('criticite') == 'Basse' ? 'selected' : '' }}>Basse</option>
                            <option value="Moyenne" {{ old('criticite') == 'Moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="Haute" {{ old('criticite') == 'Haute' ? 'selected' : '' }}>Haute</option>
                            <option value="Critique" {{ old('criticite') == 'Critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fa-solid fa-link me-2"></i>Rattachement</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Équipement Principal</label>
                        <select name="equipement_principal_id" class="form-select">
                            <option value="">Aucun équipement principal</option>
                            @foreach($equipements as $equip)
                                <option value="{{ $equip->id }}" {{ old('equipement_principal_id') == $equip->id ? 'selected' : '' }}>
                                    {{ $equip->code }} - {{ $equip->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client ID (Optionnel)</label>
                        <input type="number" name="client_id" class="form-control" value="{{ old('client_id') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-section">
                <h3><i class="fa-solid fa-users me-2"></i>Responsabilités</h3>
                <div class="mb-3">
                    <label class="form-label">Propriétaire Interne ID</label>
                    <input type="number" name="proprietaire_interne_id" class="form-control" value="{{ old('proprietaire_interne_id') }}">
                    <small class="text-muted">ID de l'utilisateur responsable (ex: 1).</small>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-gmao-primary btn-lg"><i class="fa-solid fa-save me-2"></i>Enregistrer</button>
                    <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary"><i class="fa-solid fa-times me-2"></i>Annuler</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
