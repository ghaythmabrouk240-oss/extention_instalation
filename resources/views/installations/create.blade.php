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
                <h3><i class="fa-solid fa-circle-info me-2"></i>Informations generales</h3>
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
                        <select name="type_profil" id="type_profil" class="form-select @error('type_profil') is-invalid @enderror" required>
                            <option value="">Selectionner...</option>
                            <option value="IRM" {{ old('type_profil') == 'IRM' ? 'selected' : '' }}>IRM</option>
                            <option value="CATHETERISME" {{ old('type_profil') == 'CATHETERISME' ? 'selected' : '' }}>Catheterisme</option>
                        </select>
                        @error('type_profil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut initial <span class="text-danger">*</span></label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            @foreach(\App\Models\Installation::standardStatuses() as $statut)
                                <option value="{{ $statut }}" {{ old('statut', 'Brouillon') == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                            @endforeach
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Criticite</label>
                        <select name="criticite" class="form-select">
                            <option value="">Selectionner...</option>
                            @foreach(['Basse', 'Moyenne', 'Haute', 'Critique'] as $criticite)
                                <option value="{{ $criticite }}" {{ old('criticite') == $criticite ? 'selected' : '' }}>{{ $criticite }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fa-solid fa-calendar-days me-2"></i>Planification</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Debut prevu</label>
                        <input type="date" name="planned_start_date" class="form-control" value="{{ old('planned_start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fin prevue</label>
                        <input type="date" name="planned_end_date" class="form-control" value="{{ old('planned_end_date') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Debut reel</label>
                        <input type="date" name="actual_start_date" class="form-control" value="{{ old('actual_start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fin reelle</label>
                        <input type="date" name="actual_end_date" class="form-control" value="{{ old('actual_end_date') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Note / action calendrier</label>
                    <textarea name="calendar_note" class="form-control" rows="2" placeholder="Ex: reception salle, tests qualite, controle radioprotection">{{ old('calendar_note') }}</textarea>
                </div>
            </div>

            <div class="form-section" id="cathlab-fields">
                <h3><i class="fa-solid fa-heart-pulse me-2"></i>Profil Salle de Catheterisme</h3>
                @include('installations.partials.cathlab-fields')
            </div>

            <div class="form-section">
                <h3><i class="fa-solid fa-link me-2"></i>Rattachement</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Equipement principal</label>
                        <select name="equipement_principal_id" class="form-select">
                            <option value="">Aucun equipement principal</option>
                            @foreach($equipements as $equip)
                                <option value="{{ $equip->id }}" {{ old('equipement_principal_id') == $equip->id ? 'selected' : '' }}>
                                    {{ $equip->code }} - {{ $equip->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client / site</label>
                        <select name="client_id" class="form-select">
                            <option value="">Aucun client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Equipements secondaires</label>
                    <select name="equipements_secondaires[]" class="form-select" multiple size="5">
                        @foreach($equipements as $equip)
                            <option value="{{ $equip->id }}" @selected(collect(old('equipements_secondaires', []))->contains($equip->id))>
                                {{ $equip->code }} - {{ $equip->designation }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-section">
                <h3><i class="fa-solid fa-users me-2"></i>Responsabilites</h3>
                <div class="mb-3">
                    <label class="form-label">Responsable interne</label>
                    <select name="proprietaire_interne_id" class="form-select">
                        <option value="">Non assigne</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('proprietaire_interne_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-gmao-primary btn-lg"><i class="fa-solid fa-save me-2"></i>Enregistrer</button>
                    <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary"><i class="fa-solid fa-times me-2"></i>Annuler</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const typeProfil = document.getElementById('type_profil');
    const cathFields = document.getElementById('cathlab-fields');

    function toggleCathFields() {
        cathFields.style.display = typeProfil.value === 'CATHETERISME' ? 'block' : 'none';
    }

    typeProfil.addEventListener('change', toggleCathFields);
    toggleCathFields();
</script>
@endsection
