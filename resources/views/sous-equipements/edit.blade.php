@extends('layouts.app')

@section('title', 'Modifier Sous-Equipement')

@section('content')
<div class="page-header">
    <h1>Modifier Sous-Equipement</h1>
</div>

<form action="{{ route('sous-equipements.update', $sousEquipement) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Identifiant</label>
                <input type="text" name="identifiant" class="form-control" value="{{ old('identifiant', $sousEquipement->identifiant) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Designation</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $sousEquipement->designation) }}" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Marque</label>
                <input type="text" name="marque" class="form-control" value="{{ old('marque', $sousEquipement->marque) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Modele</label>
                <input type="text" name="modele" class="form-control" value="{{ old('modele', $sousEquipement->modele) }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required>{{ old('description', $sousEquipement->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Equipement parent</label>
            <select name="equipement_id" class="form-select" required>
                @foreach($equipements as $equip)
                    <option value="{{ $equip->id }}" {{ old('equipement_id', $sousEquipement->equipement_id) == $equip->id ? 'selected' : '' }}>
                        {{ $equip->code }} - {{ $equip->designation }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-gmao-primary">Mettre a jour</button>
    <a href="{{ route('sous-equipements.index') }}" class="btn btn-gmao-secondary">Annuler</a>
</form>
@endsection
