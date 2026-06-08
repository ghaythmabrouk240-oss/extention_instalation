@extends('layouts.app')

@section('title', 'Modifier Installation: ' . $installation->nom)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Modifier Installation: {{ $installation->code_installation }}</h1>
        <p class="breadcrumb"><a href="{{ route('installations.index') }}">Installations</a> > Modifier</p>
    </div>
</div>

<form action="{{ route('installations.update', $installation) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-section">
                <h3><i class="fa-solid fa-circle-info me-2"></i>Informations Générales</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Code Installation <span class="text-danger">*</span></label>
                        <input type="text" name="code_installation" class="form-control @error('code_installation') is-invalid @enderror" value="{{ old('code_installation', $installation->code_installation) }}" required>
                        @error('code_installation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $installation->nom) }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Type de Profil <span class="text-danger">*</span></label>
                        <select name="type_profil" class="form-select @error('type_profil') is-invalid @enderror" required>
                            <option value="IRM" {{ old('type_profil', $installation->type_profil) == 'IRM' ? 'selected' : '' }}>IRM</option>
                            <option value="CATHETERISME" {{ old('type_profil', $installation->type_profil) == 'CATHETERISME' ? 'selected' : '' }}>Cathétérisme</option>
                        </select>
                        @error('type_profil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            @php $statuts = ['Brouillon', 'En validation', 'Installé', 'Opérationnel', 'En maintenance', 'Temporairement indisponible', 'Archivé']; @endphp
                            @foreach($statuts as $statut)
                                <option value="{{ $statut }}" {{ old('statut', $installation->statut) == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                            @endforeach
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Criticité</label>
                        <select name="criticite" class="form-select">
                            <option value="">Sélectionner...</option>
                            @php $critics = ['Basse', 'Moyenne', 'Haute', 'Critique']; @endphp
                            @foreach($critics as $critic)
                                <option value="{{ $critic }}" {{ old('criticite', $installation->criticite) == $critic ? 'selected' : '' }}>{{ $critic }}</option>
                            @endforeach
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
                                <option value="{{ $equip->id }}" {{ old('equipement_principal_id', $installation->equipement_principal_id) == $equip->id ? 'selected' : '' }}>
                                    {{ $equip->code }} - {{ $equip->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client ID</label>
                        <input type="number" name="client_id" class="form-control" value="{{ old('client_id', $installation->client_id) }}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-section">
                <h3><i class="fa-solid fa-users me-2"></i>Responsabilités</h3>
                <div class="mb-3">
                    <label class="form-label">Propriétaire Interne ID</label>
                    <input type="number" name="proprietaire_interne_id" class="form-control" value="{{ old('proprietaire_interne_id', $installation->proprietaire_interne_id) }}">
                </div>
                
                <hr>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-gmao-primary btn-lg"><i class="fa-solid fa-save me-2"></i>Mettre à jour</button>
                    <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary"><i class="fa-solid fa-times me-2"></i>Annuler</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
