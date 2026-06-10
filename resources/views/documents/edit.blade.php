@extends('layouts.app')

@section('title', 'Modifier Document')

@section('content')
<div class="page-header">
    <h1>Modifier Document</h1>
</div>

<form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Installation</label>
                <select name="installation_id" class="form-select" required>
                    @foreach($installations as $inst)
                        <option value="{{ $inst->id }}" {{ old('installation_id', $document->installation_id) == $inst->id ? 'selected' : '' }}>{{ $inst->code_installation }} - {{ $inst->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Categorie</label>
                <select name="categorie" class="form-select" required>
                    @foreach(\App\Models\Installation::uploadableReportCategories() as $report)
                        <option value="{{ $report['categorie'] }}" {{ old('categorie', $document->categorie) == $report['categorie'] ? 'selected' : '' }}>{{ $report['categorie'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Type de rapport</label>
                <select name="type_rapport" class="form-select">
                    <option value="">Document simple</option>
                    <option value="installation_generale" {{ old('type_rapport', $document->type_rapport) == 'installation_generale' ? 'selected' : '' }}>Installation generale</option>
                    <option value="rapport_tests" {{ old('type_rapport', $document->type_rapport) == 'rapport_tests' ? 'selected' : '' }}>Rapport des tests</option>
                    <option value="document_requis" {{ old('type_rapport', $document->type_rapport) == 'document_requis' ? 'selected' : '' }}>Document requis</option>
                    <option value="rapport_technique" {{ old('type_rapport', $document->type_rapport) == 'rapport_technique' ? 'selected' : '' }}>Rapport technique</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Version</label>
                <input type="text" name="version" class="form-control" value="{{ old('version', $document->version) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select" required>
                    @foreach(['Brouillon', 'Valide', 'A remplacer', 'Archive'] as $statut)
                        <option value="{{ $statut }}" {{ old('statut', $document->statut) == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Profil concerne</label>
                <select name="profil_concerne" class="form-select" required>
                    @foreach(['COMMUN', 'IRM', 'CATHETERISME'] as $profil)
                        <option value="{{ $profil }}" {{ old('profil_concerne', $document->profil_concerne) == $profil ? 'selected' : '' }}>{{ $profil }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Details du rapport</label>
            <textarea name="description" class="form-control" rows="5">{{ old('description', $document->description) }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Reference DMS</label>
                <input type="text" name="reference_dms" class="form-control" value="{{ old('reference_dms', $document->reference_dms) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Reference fichier POC</label>
                <input type="text" name="reference_fichier" class="form-control" value="{{ old('reference_fichier', $document->reference_fichier) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Remplacer le fichier scanne / photo / PDF</label>
            <input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            @if($document->fichier_path)
                <small class="text-muted">Fichier actuel: {{ $document->fichier_original_name }}</small>
            @endif
        </div>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="est_bloquant" id="bloquant" {{ old('est_bloquant', $document->est_bloquant) ? 'checked' : '' }}>
                <label class="form-check-label" for="bloquant">Document bloquant</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="est_version_active" id="active" {{ old('est_version_active', $document->est_version_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Version active</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-gmao-primary">Mettre a jour</button>
    <a href="{{ route('documents.index') }}" class="btn btn-gmao-secondary">Annuler</a>
</form>
@endsection
