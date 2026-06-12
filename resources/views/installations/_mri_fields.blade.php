@php($profile = $installation->profilIrm ?? null)

<div id="mri-profile-section" class="form-section">
    <h3><i class="fa-solid fa-magnet me-2"></i>Profil spécifique IRM</h3>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Champ magnétique</label>
            <input type="text" name="profil_irm[champ_magnetique]" class="form-control" value="{{ old('profil_irm.champ_magnetique', $profile?->champ_magnetique) }}" placeholder="Ex: 1.5T / 3T">
        </div>
        <div class="col-md-4">
            <label class="form-label">Blindage</label>
            <input type="text" name="profil_irm[blindage]" class="form-control" value="{{ old('profil_irm.blindage', $profile?->blindage) }}" placeholder="Ex: RF conforme">
        </div>
        <div class="col-md-4">
            <label class="form-label">Atelier / Local technique</label>
            <input type="text" name="profil_irm[atelier]" class="form-control" value="{{ old('profil_irm.atelier', $profile?->atelier) }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Bâtiment</label>
            <input type="text" name="profil_irm[batiment]" class="form-control" value="{{ old('profil_irm.batiment', $profile?->batiment) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Étage</label>
            <input type="text" name="profil_irm[etage]" class="form-control" value="{{ old('profil_irm.etage', $profile?->etage) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Zone</label>
            <input type="text" name="profil_irm[zone]" class="form-control" value="{{ old('profil_irm.zone', $profile?->zone) }}" placeholder="Ex: Zone contrôlée">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-check">
                <input type="hidden" name="profil_irm[zone_controlee]" value="0">
                <input class="form-check-input" type="checkbox" name="profil_irm[zone_controlee]" value="1" id="zone_controlee" @checked(old('profil_irm.zone_controlee', $profile?->zone_controlee))>
                <label class="form-check-label" for="zone_controlee">Zone contrôlée</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input type="hidden" name="profil_irm[confinement_ferromagnetique]" value="0">
                <input class="form-check-input" type="checkbox" name="profil_irm[confinement_ferromagnetique]" value="1" id="confinement_ferromagnetique" @checked(old('profil_irm.confinement_ferromagnetique', $profile?->confinement_ferromagnetique))>
                <label class="form-check-label" for="confinement_ferromagnetique">Confinement ferromagnétique</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input type="hidden" name="profil_irm[arret_urgence]" value="0">
                <input class="form-check-input" type="checkbox" name="profil_irm[arret_urgence]" value="1" id="arret_urgence" @checked(old('profil_irm.arret_urgence', $profile?->arret_urgence))>
                <label class="form-check-label" for="arret_urgence">Arrêt urgence</label>
            </div>
        </div>
    </div>

    <small class="text-muted d-block mt-3">
        Pour passer en validation, renseigner au minimum champ magnétique, blindage, bâtiment et zone.
    </small>
</div>
