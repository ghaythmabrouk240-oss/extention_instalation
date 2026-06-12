@php($cat = $cat ?? null)

<h4 class="text-primary mb-3">Identification salle</h4>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Departement <span class="text-danger">*</span></label>
        <input type="text" name="departement" class="form-control" value="{{ old('departement', $cat?->departement) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Batiment <span class="text-danger">*</span></label>
        <input type="text" name="batiment" class="form-control" value="{{ old('batiment', $cat?->batiment) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Etage <span class="text-danger">*</span></label>
        <input type="text" name="etage" class="form-control" value="{{ old('etage', $cat?->etage) }}" required>
    </div>
</div>

<h4 class="text-primary mb-3">Equipement principal</h4>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Systeme angiographie <span class="text-danger">*</span></label>
        <input type="text" name="systeme_angiographie" class="form-control" value="{{ old('systeme_angiographie', $cat?->systeme_angiographie) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Station de controle <span class="text-danger">*</span></label>
        <input type="text" name="station_controle" class="form-control" value="{{ old('station_controle', $cat?->station_controle) }}" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Table patient <span class="text-danger">*</span></label>
        <input type="text" name="table_patient" class="form-control" value="{{ old('table_patient', $cat?->table_patient) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Injecteur <span class="text-danger">*</span></label>
        <input type="text" name="injecteur" class="form-control" value="{{ old('injecteur', $cat?->injecteur) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Moniteurs <span class="text-danger">*</span></label>
        <input type="text" name="moniteurs" class="form-control" value="{{ old('moniteurs', $cat?->moniteurs) }}" required>
    </div>
</div>

<h4 class="text-primary mb-3">Infrastructure</h4>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Alimentation <span class="text-danger">*</span></label>
        <input type="text" name="alimentation" class="form-control" value="{{ old('alimentation', $cat?->alimentation) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Reseau <span class="text-danger">*</span></label>
        <input type="text" name="reseau" class="form-control" value="{{ old('reseau', $cat?->reseau) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Ventilation <span class="text-danger">*</span></label>
        <input type="text" name="ventilation" class="form-control" value="{{ old('ventilation', $cat?->ventilation) }}" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Radioprotection <span class="text-danger">*</span></label>
        <input type="text" name="radioprotection" class="form-control" value="{{ old('radioprotection', $cat?->radioprotection) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Protection murale <span class="text-danger">*</span></label>
        <input type="text" name="protection_murale" class="form-control" value="{{ old('protection_murale', $cat?->protection_murale) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Stockage consommables <span class="text-danger">*</span></label>
        <input type="text" name="stockage_consommables" class="form-control" value="{{ old('stockage_consommables', $cat?->stockage_consommables) }}" required>
    </div>
</div>

<h4 class="text-primary mb-3">Securite</h4>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Signalisation rayonnement <span class="text-danger">*</span></label>
        <input type="text" name="signalisation_rayonnement" class="form-control" value="{{ old('signalisation_rayonnement', $cat?->signalisation_rayonnement) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Conformite salle interventionnelle <span class="text-danger">*</span></label>
        <input type="text" name="conformite_salle_interventionnelle" class="form-control" value="{{ old('conformite_salle_interventionnelle', $cat?->conformite_salle_interventionnelle) }}" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Dispositifs de securite <span class="text-danger">*</span></label>
        <input type="text" name="dispositifs_securite" class="form-control" value="{{ old('dispositifs_securite', $cat?->dispositifs_securite) }}" required>
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="hidden" name="controle_acces" value="0">
            <input class="form-check-input" type="checkbox" name="controle_acces" id="controle_acces" value="1" {{ old('controle_acces', $cat?->controle_acces) ? 'checked' : '' }}>
            <label class="form-check-label" for="controle_acces">Controle d'acces disponible</label>
        </div>
    </div>
</div>
