@extends('layouts.app')

@section('title', 'Nouveau Sous-Équipement')

@section('content')
<div class="page-header"><h1>Nouveau Sous-Équipement</h1></div>
<form action="{{ route('sous-equipements.store') }}" method="POST">
    @csrf
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Identifiant</label>
                <input type="text" name="identifiant" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Désignation</label>
                <input type="text" name="designation" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Marque</label>
                <input type="text" name="marque" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Modèle</label>
                <input type="text" name="modele" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Équipement Parent</label>
            <select name="equipement_id" class="form-select" required>
                @foreach($equipements as $equip)
                    <option value="{{ $equip->id }}">{{ $equip->code }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-gmao-primary">Enregistrer</button>
</form>
@endsection
