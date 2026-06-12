@extends('layouts.app')

@section('title', 'Modifier Document')

@section('content')
<div class="page-header">
    <h1>Modifier Document</h1>
    @if(session('test_message'))
        <p class="text-danger">{{ session('test_message') }}</p>
    @endif
</div>

<form action="{{ url('documents/' . $document->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Installation <span class="text-danger">*</span></label>
                <select name="installation_id" class="form-select @error('installation_id') is-invalid @enderror" required>
                    @foreach($installations as $inst)
                        <option value="{{ $inst->id }}" {{ old('installation_id', $document->installation_id) == $inst->id ? 'selected' : '' }}>{{ $inst->code_installation }} - {{ $inst->nom }}</option>
                    @endforeach
                </select>
                @error('installation_id')
                    <div class="invalid-feedback">Veuillez selectionner une installation.</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Categorie <span class="text-danger">*</span></label>
                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                    @foreach(\App\Models\Installation::uploadableReportCategories() as $report)
                        <option value="{{ $report['categorie'] }}" {{ old('categorie', $document->categorie) == $report['categorie'] ? 'selected' : '' }}>{{ $report['categorie'] }}</option>
                    @endforeach
                </select>
                @error('categorie')
                    <div class="invalid-feedback">Veuillez selectionner une categorie.</div>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Type de rapport</label>
                <select name="type_rapport" class="form-select @error('type_rapport') is-invalid @enderror">
                    <option value="">Document simple</option>
                    <option value="installation_generale" {{ old('type_rapport', $document->type_rapport) == 'installation_generale' ? 'selected' : '' }}>Installation generale</option>
                    <option value="rapport_tests" {{ old('type_rapport', $document->type_rapport) == 'rapport_tests' ? 'selected' : '' }}>Rapport des tests</option>
                    <option value="document_requis" {{ old('type_rapport', $document->type_rapport) == 'document_requis' ? 'selected' : '' }}>Document requis</option>
                    <option value="rapport_technique" {{ old('type_rapport', $document->type_rapport) == 'rapport_technique' ? 'selected' : '' }}>Rapport technique</option>
                </select>
                @error('type_rapport')
                    <div class="invalid-feedback">Le type de rapport doit etre une valeur valide.</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Version <span class="text-danger">*</span></label>
                <input type="text" name="version" class="form-control @error('version') is-invalid @enderror" value="{{ old('version', $document->version) }}" required>
                @error('version')
                    <div class="invalid-feedback">La version est requise (ex: 1.0, 2.1).</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut <span class="text-danger">*</span></label>
                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                    @foreach(['Brouillon', 'Valide', 'A remplacer', 'Archive'] as $statut)
                        <option value="{{ $statut }}" {{ old('statut', $document->statut) == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                    @endforeach
                </select>
                @error('statut')
                    <div class="invalid-feedback">Veuillez selectionner un statut.</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Profil concerne <span class="text-danger">*</span></label>
                <select name="profil_concerne" class="form-select @error('profil_concerne') is-invalid @enderror" required>
                    @foreach(['COMMUN', 'IRM', 'CATHETERISME'] as $profil)
                        <option value="{{ $profil }}" {{ old('profil_concerne', $document->profil_concerne) == $profil ? 'selected' : '' }}>{{ $profil }}</option>
                    @endforeach
                </select>
                @error('profil_concerne')
                    <div class="invalid-feedback">Le profil doit etre IRM, CATHETERISME ou COMMUN.</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Details du rapport</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $document->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">La description ne peut pas depasser la longueur maximale.</div>
            @enderror
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Reference DMS</label>
                <input type="text" name="reference_dms" class="form-control @error('reference_dms') is-invalid @enderror" value="{{ old('reference_dms', $document->reference_dms) }}">
                @error('reference_dms')
                    <div class="invalid-feedback">La reference DMS ne peut pas depasser 255 caracteres.</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Reference fichier POC</label>
                <input type="text" name="reference_fichier" class="form-control @error('reference_fichier') is-invalid @enderror" value="{{ old('reference_fichier', $document->reference_fichier) }}">
                @error('reference_fichier')
                    <div class="invalid-feedback">La reference fichier ne peut pas depasser 255 caracteres.</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Remplacer le fichier scanne / photo / PDF</label>
            <input type="file" name="fichier" class="form-control @error('fichier') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
            @if($document->fichier_path)
                <small class="text-muted">Fichier actuel: {{ $document->fichier_original_name }}</small>
            @endif
            @error('fichier')
                <div class="invalid-feedback">Le fichier doit etre au format PDF, JPG ou PNG et ne pas depasser 10 Mo.</div>
            @enderror
        </div>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input type="hidden" name="est_bloquant" value="0">
                <input class="form-check-input" type="checkbox" name="est_bloquant" id="bloquant" value="1" {{ old('est_bloquant', $document->est_bloquant) ? 'checked' : '' }}>
                <label class="form-check-label" for="bloquant">Document bloquant</label>
            </div>
            <div class="form-check">
                <input type="hidden" name="est_version_active" value="0">
                <input class="form-check-input" type="checkbox" name="est_version_active" id="active" value="1" {{ old('est_version_active', $document->est_version_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Version active</label>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-gmao-primary">Mettre a jour</button>
            <a href="{{ route('documents.index') }}" class="btn btn-gmao-secondary">Annuler</a>
        </div>
    </div>
</form>
@endsection
