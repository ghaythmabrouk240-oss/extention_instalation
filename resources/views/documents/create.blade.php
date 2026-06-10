@extends('layouts.app')

@section('title', 'Nouveau Document')

@section('content')
<div class="page-header">
    <h1>Nouveau Document</h1>
</div>

<form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($selectedRedirectTo)
        <input type="hidden" name="redirect_to" value="{{ $selectedRedirectTo }}">
    @endif
    @if($requiresFile)
        <input type="hidden" name="requires_file" value="1">
    @endif
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Installation</label>
                <select name="installation_id" class="form-select" required>
                    @foreach($installations as $inst)
                        <option value="{{ $inst->id }}" {{ old('installation_id', $selectedInstallationId) == $inst->id ? 'selected' : '' }}>{{ $inst->code_installation }} - {{ $inst->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Categorie</label>
                <select name="categorie" class="form-select" required>
                    @foreach(\App\Models\Installation::uploadableReportCategories() as $report)
                        <option value="{{ $report['categorie'] }}" {{ old('categorie', $selectedCategorie) == $report['categorie'] ? 'selected' : '' }}>{{ $report['categorie'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Type de rapport</label>
                <select name="type_rapport" class="form-select">
                    <option value="">Document simple</option>
                    <option value="installation_generale" {{ old('type_rapport', $selectedTypeRapport) == 'installation_generale' ? 'selected' : '' }}>Installation generale</option>
                    <option value="rapport_tests" {{ old('type_rapport', $selectedTypeRapport) == 'rapport_tests' ? 'selected' : '' }}>Rapport des tests</option>
                    <option value="document_requis" {{ old('type_rapport', $selectedTypeRapport) == 'document_requis' ? 'selected' : '' }}>Document requis</option>
                    <option value="rapport_technique" {{ old('type_rapport', $selectedTypeRapport) == 'rapport_technique' ? 'selected' : '' }}>Rapport technique</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Version</label>
                <input type="text" name="version" class="form-control" value="{{ old('version', '1.0') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select" required>
                    @foreach(['Brouillon', 'Valide', 'A remplacer', 'Archive'] as $statut)
                        <option value="{{ $statut }}" {{ old('statut', 'Valide') == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Profil concerne</label>
                <select name="profil_concerne" class="form-select" required>
                    @foreach(['COMMUN', 'IRM', 'CATHETERISME'] as $profil)
                        <option value="{{ $profil }}" {{ old('profil_concerne', 'COMMUN') == $profil ? 'selected' : '' }}>{{ $profil }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Details du rapport</label>
            <textarea name="description" class="form-control" rows="5" placeholder="Identification: salle, departement, batiment, etage, criticite, responsable interne. Equipement principal: systeme angiographie, table patient, station de controle. Infrastructure: alimentation, reseau, ventilation, radioprotection, protection murale. Securite: signalisation, controle d'acces, conformite, dispositifs.">{{ old('description') }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Reference DMS</label>
                <input type="text" name="reference_dms" class="form-control" value="{{ old('reference_dms') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Reference fichier POC</label>
                <input type="text" name="reference_fichier" class="form-control" value="{{ old('reference_fichier') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">
                Fichier scanne / photo / PDF
                @if($requiresFile)<span class="text-danger">*</span>@endif
            </label>
            <input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" @if($requiresFile) required @endif>
            <small class="text-muted">Formats acceptes: PDF, JPG, PNG (rapport manuscrit scanne ou photo). Taille max: 10 Mo.</small>
        </div>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="est_bloquant" id="bloquant" {{ old('est_bloquant') ? 'checked' : '' }}>
                <label class="form-check-label" for="bloquant">Document bloquant</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="est_version_active" id="active" checked>
                <label class="form-check-label" for="active">Version active</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-gmao-primary">Enregistrer</button>
    @if($selectedRedirectTo === 'installation' && $selectedInstallationId)
        <a href="{{ route('installations.show', $selectedInstallationId) }}" class="btn btn-gmao-secondary">Annuler</a>
    @else
        <a href="{{ route('documents.index') }}" class="btn btn-gmao-secondary">Annuler</a>
    @endif
</form>
@endsection
