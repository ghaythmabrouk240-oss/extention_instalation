@extends('layouts.app')

@section('title', 'Nouveau Document')

@section('content')
<div class="page-header">
    <h1>Nouveau Document</h1>
</div>

<form action="{{ route('documents.store') }}" method="POST">
    @csrf
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Installation</label>
                <select name="installation_id" class="form-select" required>
                    @foreach($installations as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->code_installation }} - {{ $inst->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Catégorie</label>
                <input type="text" name="categorie" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Version</label>
                <input type="text" name="version" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <input type="text" name="statut" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="est_bloquant" id="bloquant">
                    <label class="form-check-label" for="bloquant">Est Bloquant</label>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-gmao-primary">Enregistrer</button>
</form>
@endsection
