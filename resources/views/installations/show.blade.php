@extends('layouts.app')

@section('title', 'Detail Installation: ' . $installation->code_installation)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Detail: {{ $installation->nom }} ({{ $installation->code_installation }})</h1>
        <p class="breadcrumb"><a href="{{ route('installations.index') }}">Installations</a> > {{ $installation->code_installation }}</p>
    </div>
    <div>
        <a href="{{ route('installations.budget', $installation) }}" class="btn btn-gmao-secondary me-2">
            <i class="fa-solid fa-calculator me-2"></i>Budget
        </a>
        <a href="{{ route('installations.export', $installation) }}" class="btn btn-gmao-secondary me-2">
            <i class="fa-solid fa-file-export me-2"></i>Exporter
        </a>
        <a href="{{ route('installations.edit', $installation) }}" class="btn btn-gmao-warning me-2">
            <i class="fa-solid fa-pen me-2"></i>Modifier
        </a>
        <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-tag"></i></div>
            <div class="stat-value">{{ $installation->type_profil }}</div>
            <div class="stat-label">Type Profil</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-signal"></i></div>
            <div class="stat-value">{{ $installation->statut }}</div>
            <div class="stat-label">Statut Actuel</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-microchip"></i></div>
            <div class="stat-value">{{ $installation->equipements->count() + ($installation->equipementPrincipal ? 1 : 0) }}</div>
            <div class="stat-label">Equipements Lies</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-file-pdf"></i></div>
            <div class="stat-value">{{ count($missingRequiredDocuments) }}</div>
            <div class="stat-label">Docs requis manquants</div>
        </div>
    </div>
</div>

@if($missingRequiredDocuments)
    <div class="alert alert-warning">
        Documents requis manquants: {{ implode(', ', $missingRequiredDocuments) }}
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-3" id="installationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab">Profil Specifique</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="equipements-tab" data-bs-toggle="tab" data-bs-target="#equipements" type="button" role="tab">Equipements & Sous-equipements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="interventions-tab" data-bs-toggle="tab" data-bs-target="#interventions" type="button" role="tab">Interventions</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">Historique</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">Audit</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="budget-tab" data-bs-toggle="tab" data-bs-target="#budget" type="button" role="tab">Budget</button>
            </li>
        </ul>

        <div class="tab-content p-4" id="installationTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary"><i class="fa-solid fa-info-circle me-2"></i>Identite</h4>
                        <ul class="detail-list">
                            <li><span class="detail-label">Code Installation</span><span class="detail-value">{{ $installation->code_installation }}</span></li>
                            <li><span class="detail-label">Nom</span><span class="detail-value">{{ $installation->nom }}</span></li>
                            <li><span class="detail-label">Type Profil</span><span class="detail-value">{{ $installation->type_profil }}</span></li>
                            <li><span class="detail-label">Criticite</span><span class="detail-value">{{ $installation->criticite ?? 'Non defini' }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary"><i class="fa-solid fa-calendar-days me-2"></i>Planification</h4>
                        <ul class="detail-list">
                            <li><span class="detail-label">Debut prevu</span><span class="detail-value">{{ optional($installation->planned_start_date)->format('d/m/Y') ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Fin prevue</span><span class="detail-value">{{ optional($installation->planned_end_date)->format('d/m/Y') ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Debut reel</span><span class="detail-value">{{ optional($installation->actual_start_date)->format('d/m/Y') ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Fin reelle</span><span class="detail-value">{{ optional($installation->actual_end_date)->format('d/m/Y') ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Note calendrier</span><span class="detail-value">{{ $installation->calendar_note ?? 'Non defini' }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary"><i class="fa-solid fa-network-wired me-2"></i>Rattachement</h4>
                        <ul class="detail-list">
                            <li><span class="detail-label">Client / site</span><span class="detail-value">{{ $installation->client?->nom ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Equipement Principal</span><span class="detail-value">{{ $installation->equipementPrincipal ? $installation->equipementPrincipal->code . ' - ' . $installation->equipementPrincipal->designation : 'Non defini' }}</span></li>
                            <li><span class="detail-label">Responsable interne</span><span class="detail-value">{{ $installation->proprietaireInterne?->name ?? 'Non defini' }}</span></li>
                            <li><span class="detail-label">Cree le</span><span class="detail-value">{{ $installation->created_at->format('d/m/Y H:i') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="profil" role="tabpanel">
                @if($installation->type_profil === 'CATHETERISME' && $installation->profilCatLab)
                    @php($cat = $installation->profilCatLab)
                    <h4 class="mb-3 text-primary">Profil Salle de Catheterisme</h4>

                    <h5 class="text-secondary">Identification</h5>
                    <ul class="detail-list mb-4">
                        <li><span class="detail-label">Nom de salle</span><span class="detail-value">{{ $installation->nom }}</span></li>
                        <li><span class="detail-label">Departement</span><span class="detail-value">{{ $cat->departement }}</span></li>
                        <li><span class="detail-label">Batiment</span><span class="detail-value">{{ $cat->batiment }}</span></li>
                        <li><span class="detail-label">Etage</span><span class="detail-value">{{ $cat->etage }}</span></li>
                        <li><span class="detail-label">Criticite</span><span class="detail-value">{{ $installation->criticite ?? 'Non defini' }}</span></li>
                        <li><span class="detail-label">Responsable interne</span><span class="detail-value">{{ $installation->proprietaireInterne?->name ?? 'Non defini' }}</span></li>
                    </ul>

                    <h5 class="text-secondary">Equipement principal</h5>
                    <ul class="detail-list mb-4">
                        <li><span class="detail-label">Equipement principal</span><span class="detail-value">{{ $installation->equipementPrincipal?->code ?? 'Non defini' }}</span></li>
                        <li><span class="detail-label">Systeme angiographie</span><span class="detail-value">{{ $cat->systeme_angiographie }}</span></li>
                        <li><span class="detail-label">Table patient</span><span class="detail-value">{{ $cat->table_patient }}</span></li>
                        <li><span class="detail-label">Station de controle</span><span class="detail-value">{{ $cat->station_controle }}</span></li>
                        <li><span class="detail-label">Injecteur</span><span class="detail-value">{{ $cat->injecteur }}</span></li>
                        <li><span class="detail-label">Moniteurs</span><span class="detail-value">{{ $cat->moniteurs }}</span></li>
                    </ul>

                    <h5 class="text-secondary">Infrastructure</h5>
                    <ul class="detail-list mb-4">
                        <li><span class="detail-label">Alimentation</span><span class="detail-value">{{ $cat->alimentation }}</span></li>
                        <li><span class="detail-label">Reseau</span><span class="detail-value">{{ $cat->reseau }}</span></li>
                        <li><span class="detail-label">Ventilation</span><span class="detail-value">{{ $cat->ventilation }}</span></li>
                        <li><span class="detail-label">Radioprotection</span><span class="detail-value">{{ $cat->radioprotection }}</span></li>
                        <li><span class="detail-label">Protection murale</span><span class="detail-value">{{ $cat->protection_murale }}</span></li>
                        <li><span class="detail-label">Stockage consommables</span><span class="detail-value">{{ $cat->stockage_consommables }}</span></li>
                    </ul>

                    <h5 class="text-secondary">Securite</h5>
                    <ul class="detail-list">
                        <li><span class="detail-label">Signalisation rayonnement</span><span class="detail-value">{{ $cat->signalisation_rayonnement }}</span></li>
                        <li><span class="detail-label">Controle d'acces</span><span class="detail-value">{{ $cat->controle_acces ? 'Oui' : 'Non' }}</span></li>
                        <li><span class="detail-label">Conformite salle interventionnelle</span><span class="detail-value">{{ $cat->conformite_salle_interventionnelle }}</span></li>
                        <li><span class="detail-label">Dispositifs de securite</span><span class="detail-value">{{ $cat->dispositifs_securite }}</span></li>
                    </ul>
                @else
                    <div class="alert alert-info">
                        Profil {{ $installation->type_profil }} non configure ou details non disponibles pour cette installation.
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="equipements" role="tabpanel">
                @if($installation->equipementPrincipal)
                    <h4 class="mb-3 text-primary">Equipement principal</h4>
                    <p><strong>{{ $installation->equipementPrincipal->code }}</strong> - {{ $installation->equipementPrincipal->designation }} / {{ $installation->equipementPrincipal->marque }} {{ $installation->equipementPrincipal->modele }}</p>
                    @if($installation->equipementPrincipal->sousEquipements->isNotEmpty())
                        <table class="table table-sm table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Identifiant</th>
                                    <th>Designation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installation->equipementPrincipal->sousEquipements as $sousEquipement)
                                    <tr>
                                        <td>{{ $sousEquipement->identifiant }}</td>
                                        <td>{{ $sousEquipement->designation }}</td>
                                        <td>
                                            <a href="{{ route('sous-equipements.show', $sousEquipement) }}" class="btn btn-action btn-view btn-sm">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif

                <h4 class="mb-3 text-primary">Equipements secondaires</h4>
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Designation</th>
                            <th>Marque</th>
                            <th>Modele</th>
                            <th>Sous-equipements</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->equipements as $equipement)
                        <tr>
                            <td>{{ $equipement->code }}</td>
                            <td>{{ $equipement->designation }}</td>
                            <td>{{ $equipement->marque }}</td>
                            <td>{{ $equipement->modele }}</td>
                            <td>
                                @if($equipement->sousEquipements->isNotEmpty())
                                    @foreach($equipement->sousEquipements as $sousEquipement)
                                        <a href="{{ route('sous-equipements.show', $sousEquipement) }}" class="d-block">{{ $sousEquipement->designation }}</a>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $equipement->pivot->role }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun equipement secondaire lie.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0 text-primary">Documents et rapports</h4>
                        <p class="small text-muted mb-0">Televersez un PDF ou une photo/scan d un rapport manuscrit pour chaque document requis.</p>
                    </div>
                </div>

                <div class="row mb-4">
                    @foreach($uploadableReports as $report)
                        @include('installations.partials.report-card', ['report' => $report, 'installation' => $installation])
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('documents.create', ['installation_id' => $installation->id, 'redirect_to' => 'installation']) }}" class="btn btn-gmao-secondary btn-sm">
                        <i class="fa-solid fa-plus me-2"></i>Autre document
                    </a>
                </div>
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Categorie</th>
                            <th>Type</th>
                            <th>Profil</th>
                            <th>Version</th>
                            <th>Statut</th>
                            <th>Reference</th>
                            <th>Fichier</th>
                            <th>Actif</th>
                            <th>Bloquant</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->documents as $doc)
                        <tr>
                            <td><strong>{{ $doc->categorie }}</strong></td>
                            <td>{{ $doc->type_rapport ?? '-' }}</td>
                            <td>{{ $doc->profil_concerne }}</td>
                            <td>v{{ $doc->version }}</td>
                            <td>{{ $doc->statut }}</td>
                            <td>{{ $doc->reference_dms ?? $doc->reference_fichier ?? '-' }}</td>
                            <td>
                                @if($doc->fichier_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->fichier_path) }}" target="_blank">{{ $doc->fichier_original_name ?? 'Ouvrir' }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $doc->est_version_active ? 'Oui' : 'Non' }}</td>
                            <td>{{ $doc->est_bloquant ? 'Oui' : 'Non' }}</td>
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-action btn-view btn-sm" title="Voir"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Aucun document rattache.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="interventions" role="tabpanel">
                <h4 class="mb-3 text-primary">Interventions planifiees et operationnelles</h4>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Planification</h5>
                        <ul class="detail-list mb-0">
                            <li><span class="detail-label">Periode prevue</span><span class="detail-value">{{ optional($installation->planned_start_date)->format('d/m/Y') ?? '-' }} @if($installation->planned_end_date) au {{ $installation->planned_end_date->format('d/m/Y') }} @endif</span></li>
                            <li><span class="detail-label">Action / note</span><span class="detail-value">{{ $installation->calendar_note ?? 'Aucune note' }}</span></li>
                        </ul>
                    </div>
                </div>
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->historiqueStatuts as $hist)
                            <tr>
                                <td>{{ $hist->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if(in_array($hist->nouveau_statut, ['En maintenance', 'Temporairement indisponible']))
                                        <span class="badge bg-warning text-dark">Maintenance / indisponibilite</span>
                                    @else
                                        <span class="badge bg-info text-dark">Changement statut</span>
                                    @endif
                                </td>
                                <td>{{ $hist->ancien_statut ?: '-' }} &rarr; <strong>{{ $hist->nouveau_statut }}</strong></td>
                                <td>{{ $hist->commentaire }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Aucune intervention enregistree.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="historique" role="tabpanel">
                <table class="table table-gmao table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur ID</th>
                            <th>Ancien Statut</th>
                            <th>Nouveau Statut</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installation->historiqueStatuts as $hist)
                        <tr>
                            <td>{{ $hist->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $hist->user_id }}</td>
                            <td>{{ $hist->ancien_statut ?: '-' }}</td>
                            <td><strong>{{ $hist->nouveau_statut }}</strong></td>
                            <td>{{ $hist->commentaire }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun historique disponible.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="audit" role="tabpanel">
                <h4 class="mb-3 text-primary">Audit documentaire et statuts</h4>
                <table class="table table-gmao table-hover mb-4">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Element</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($installation->historiqueStatuts as $hist)
                            <tr>
                                <td>{{ $hist->created_at->format('d/m/Y H:i') }}</td>
                                <td>Statut</td>
                                <td>{{ $hist->nouveau_statut }}</td>
                                <td>{{ $hist->commentaire }}</td>
                            </tr>
                        @endforeach
                        @foreach($installation->documents as $doc)
                            <tr>
                                <td>{{ $doc->updated_at->format('d/m/Y H:i') }}</td>
                                <td>Document</td>
                                <td>{{ $doc->categorie }} v{{ $doc->version }}</td>
                                <td>{{ $doc->statut }} {{ $doc->est_version_active ? '(actif)' : '' }}</td>
                            </tr>
                        @endforeach
                        @if($installation->historiqueStatuts->isEmpty() && $installation->documents->isEmpty())
                            <tr><td colspan="4" class="text-center">Aucune trace d audit disponible.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="budget" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 text-primary">Budget et Suivi Financier</h4>
                    <a href="{{ route('installations.budget', $installation) }}" class="btn btn-gmao-primary">
                        <i class="fa-solid fa-calculator me-2"></i>Gérer le Budget
                    </a>
                </div>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Cliquez sur "Gérer le Budget" pour accéder au suivi financier complet, ajouter des dépenses, configurer les pénalités et exporter les factures.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
